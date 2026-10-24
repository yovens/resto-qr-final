<?php

namespace App\Http\Controllers\Plat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plat;
use App\Models\Category;

class PlatController extends Controller
{
    private const MESSAGES_IMAGE = [
        'image.mimes'    => 'Photo refusée : utilisez une image JPG, PNG ou WEBP (les photos HEIC d\'iPhone ne sont pas acceptées).',
        'image.max'      => 'Photo trop lourde (15 Mo maximum).',
        'image.uploaded' => 'La photo n\'a pas pu être envoyée (trop lourde pour le serveur). Choisissez une photo plus petite.',
    ];

    /** Taille maximale (en pixels) du plus grand côté des photos enregistrées. */
    private const IMAGE_MAX_PX = 1200;

    /**
     * Enregistre la photo dans public/images :
     *  - le nom et l'extension sont choisis par le serveur (jamais ceux du fichier envoyé) ;
     *  - si l'extension GD est active, la photo est réduite à 1200 px et compressée en JPG
     *    (un menu léger s'ouvre vite sur les téléphones des clients).
     */
    private function enregistrerImage(\Illuminate\Http\UploadedFile $file): string
    {
        $dossier = public_path('images');
        if (!is_dir($dossier)) {
            mkdir($dossier, 0775, true);
        }

        $base = uniqid('plat_') . '_' . time();

        if (function_exists('imagecreatefromstring') && ($src = @imagecreatefromstring(file_get_contents($file->getRealPath())))) {
            // Corrige l'orientation des photos de téléphone
            if (function_exists('exif_read_data') && in_array($file->extension(), ['jpg', 'jpeg'], true)) {
                $exif = @exif_read_data($file->getRealPath());
                $rot  = [3 => 180, 6 => -90, 8 => 90][$exif['Orientation'] ?? 1] ?? 0;
                if ($rot) {
                    $src = imagerotate($src, $rot, 0);
                }
            }

            $w = imagesx($src);
            $h = imagesy($src);
            $ratio = min(1, self::IMAGE_MAX_PX / max($w, $h));
            $nw = max(1, (int) round($w * $ratio));
            $nh = max(1, (int) round($h * $ratio));

            $dst = imagecreatetruecolor($nw, $nh);
            imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255)); // fond blanc pour les PNG transparents
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

            $nom = $base . '.jpg';
            imagejpeg($dst, $dossier . DIRECTORY_SEPARATOR . $nom, 82);
            imagedestroy($src);
            imagedestroy($dst);

            return $nom;
        }

        // Sans GD : on garde le fichier tel quel, avec une extension sûre déduite du contenu
        $ext = in_array($file->extension(), ['jpg', 'jpeg', 'png', 'webp', 'gif'], true) ? $file->extension() : 'jpg';
        $nom = $base . '.' . $ext;
        $file->move($dossier, $nom);

        return $nom;
    }

    public function index()
    {
        $plats = Plat::with('category')->get();
        return view('admin.plats.index', compact('plats'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.plats.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required',
            'prix' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif|max:15360'
        ], self::MESSAGES_IMAGE);

        // checkbox propre
        $data['disponible'] = $request->has('disponible') ? 1 : 0;

        // upload image
        if ($request->hasFile('image')) {
            $data['image'] = $this->enregistrerImage($request->file('image'));
        }

        Plat::create($data);

        return redirect('/admin/plats')->with('success', 'Plat ajouté');
    }

    public function edit($id)
    {
        $plat = Plat::findOrFail($id);
        $categories = Category::all();

        return view('admin.plats.edit', compact('plat', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $plat = Plat::findOrFail($id);

        $data = $request->validate([
            'nom' => 'required',
            'prix' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif|max:15360'
        ], self::MESSAGES_IMAGE);

        $data['disponible'] = $request->has('disponible') ? 1 : 0;

        // update image
        if ($request->hasFile('image')) {

            $nouvelle = $this->enregistrerImage($request->file('image'));

            if ($plat->image && file_exists(public_path('images/'.$plat->image))) {
                @unlink(public_path('images/'.$plat->image));
            }

            $data['image'] = $nouvelle;
        }

        $plat->update($data);

        return redirect('/admin/plats')->with('success', 'Plat modifié');
    }

    public function destroy($id)
    {
        $plat = Plat::findOrFail($id);

        if ($plat->image && file_exists(public_path('images/'.$plat->image))) {
            unlink(public_path('images/'.$plat->image));
        }

        $plat->delete();

        return redirect('/admin/plats')->with('success', 'Plat supprimé');
    }
}