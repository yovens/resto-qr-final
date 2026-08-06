<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>
Admin Restaurant
</title>


<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">


<style>
  /* =========================
           DESIGN TOKENS
           ========================= */
        :root{
            --dark-900:#111827;
            --dark-800:#1f2937;
            --dark-700:#374151;
            --gray-500:#6b7280;
            --gray-400:#9ca3af;
            --gray-200:#e5e7eb;
            --gray-100:#f3f4f6;
            --gray-50:#f9fafb;
            --bg:#f8fafc;

            --accent:#fb923c;       /* orange resto - couleur de marque */
            --accent-dark:#ea580c;
            --accent-soft:#fff7ed;

            --info:#3b82f6;
            --info-dark:#2563eb;
            --success:#10b981;
            --success-dark:#059669;
            --warning:#f59e0b;
            --danger:#ef4444;
            --danger-dark:#dc2626;

            --radius-lg:20px;
            --radius-md:15px;
            --radius-sm:10px;

            --shadow-sm:0 5px 20px rgba(0,0,0,.05);
            --shadow-md:0 8px 25px rgba(0,0,0,.06);
            --shadow-lg:0 10px 30px rgba(0,0,0,.15);

            --gap-sm:12px;
            --gap-md:20px;
            --gap-lg:30px;

            --sidebar-w:270px;
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            font-family:'Segoe UI', sans-serif;
            background:var(--bg);
            color:var(--dark-900);
        }

        /* =========================
           SIDEBAR
           ========================= */
        .sidebar{
            position:fixed;
            left:0; top:0;
            height:100vh;
            width:var(--sidebar-w);
            background:linear-gradient(180deg, var(--dark-900), var(--dark-800));
            color:white;
            padding:25px 18px;
            z-index:1000;
            display:flex;
            flex-direction:column;
            overflow-y:auto;
            transition:.3s;
        }

        .sidebar-top{ flex:1; }

        .logo{
            display:flex;
            align-items:center;
            gap:12px;
            font-size:22px;
            font-weight:800;
            padding:0 7px;
            margin-bottom:36px;
        }
        .logo i{ color:var(--accent); font-size:28px; }

        .menu-title{
            font-size:11px;
            font-weight:700;
            letter-spacing:.06em;
            text-transform:uppercase;
            color:var(--gray-400);
            margin:24px 7px 10px;
        }
        .menu-title:first-of-type{ margin-top:0; }

        .sidebar nav a{
            display:flex;
            align-items:center;
            gap:14px;
            padding:12px 14px;
            border-radius:14px;
            color:#d1d5db;
            text-decoration:none;
            margin-bottom:4px;
            font-weight:600;
            font-size:14.5px;
            transition:background .2s, color .2s, transform .2s;
        }
        .sidebar nav a i{
            width:20px;
            text-align:center;
            font-size:15px;
            flex-shrink:0;
        }
        .sidebar nav a:hover{
            background:rgba(255,255,255,.08);
            color:#fff;
        }
        .sidebar nav a.active{
            background:var(--accent);
            color:#fff;
        }
        .sidebar nav a.active:hover{ transform:none; }

        .sidebar nav a .badge{
            margin-left:auto;
            background:var(--danger);
            color:#fff;
            padding:2px 8px;
            border-radius:20px;
            font-size:11px;
            font-weight:700;
            line-height:1.5;
        }
        .sidebar nav a .badge.pulse{ animation:pulse-anim 1.6s infinite; }

        .sidebar-footer{
            padding-top:15px;
            margin-top:10px;
            border-top:1px solid rgba(255,255,255,.1);
        }
        .btn-logout{
            width:100%;
            background:transparent;
            border:none;
            color:#f87171;
            display:flex;
            align-items:center;
            gap:10px;
            padding:10px 14px;
            cursor:pointer;
            font-size:14px;
            font-weight:700;
            border-radius:10px;
            text-align:left;
            transition:background .2s;
        }
        .btn-logout:hover{ background:rgba(248,113,113,.12); }

        /* =========================
           MAIN / TOPBAR
           ========================= */
        .main{ margin-left:var(--sidebar-w); min-height:100vh; }

        .topbar{
            height:78px;
            background:#fff;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:20px;
            padding:0 32px;
            box-shadow:var(--shadow-sm);
            position:sticky;
            top:0;
            z-index:900;
        }

        .search-box{
            display:flex;
            align-items:center;
            gap:10px;
            background:var(--gray-100);
            padding:11px 18px;
            border-radius:30px;
            width:300px;
            color:var(--gray-500);
        }
        .search-box input{
            border:none;
            background:none;
            outline:none;
            width:100%;
            font-size:14px;
        }

        .top-actions{ display:flex; align-items:center; gap:22px; }

        .notification{
            position:relative;
            font-size:20px;
            color:var(--dark-700);
            cursor:pointer;
        }
        .notification span{
            position:absolute;
            top:-6px; right:-9px;
            background:var(--danger);
            color:#fff;
            width:17px; height:17px;
            border-radius:50%;
            font-size:10px;
            font-weight:700;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .profile{ display:flex; align-items:center; gap:12px; }
        .profile-info{ line-height:1.25; }
        .profile-info strong{ font-size:14px; }
        .profile-info small{ color:var(--gray-500); font-size:12px; }

        .avatar{
            width:44px; height:44px;
            border-radius:50%;
            background:var(--accent);
            display:flex;
            align-items:center;
            justify-content:center;
            color:#fff;
            font-weight:700;
        }

        .content{ padding:30px 32px; }

        /* =========================
           MOBILE
           ========================= */
        .menu-toggle{ display:none; font-size:23px; cursor:pointer; color:var(--dark-700); }

        @media(max-width:900px){
            .sidebar{ left:calc(-1 * var(--sidebar-w) - 10px); }
            .sidebar.show{ left:0; }
            .main{ margin-left:0; }
            .menu-toggle{ display:block; }
            .search-box{ display:none; }
            .content{ padding:20px; }
        }

        /* =========================
           DASHBOARD (utilisé par @yield('content'))
           ========================= */
        .dashboard-wrapper{ font-family:'Segoe UI', sans-serif; color:var(--dark-700); }

        .dash-header-card{
            background:linear-gradient(135deg, var(--dark-900), var(--dark-800));
            color:#fff;
            padding:28px 30px;
            border-radius:var(--radius-lg);
            display:flex;
            justify-content:space-between;
            align-items:center;
            box-shadow:var(--shadow-lg);
            margin-bottom:var(--gap-lg);
            flex-wrap:wrap;
            gap:var(--gap-md);
        }
        .dash-header-card h1{ margin:0; font-size:26px; }
        .dash-header-card p{ margin:6px 0 0; color:var(--gray-400); font-size:14px; }

        .dash-actions{ display:flex; align-items:center; gap:14px; flex-wrap:wrap; }

        .clock-badge{
            background:rgba(255,255,255,.1);
            padding:10px 18px;
            border-radius:var(--radius-md);
            font-weight:700;
            display:flex;
            align-items:center;
            gap:8px;
        }

        .btn-refresh, .btn-close-day{
            border:none;
            padding:11px 20px;
            border-radius:var(--radius-md);
            cursor:pointer;
            font-weight:700;
            color:#fff;
            transition:transform .2s, box-shadow .2s;
        }
        .btn-refresh{ background:var(--info); }
        .btn-refresh:hover{ background:var(--info-dark); transform:translateY(-2px); }

        .btn-close-day{
            background:linear-gradient(135deg, var(--accent), var(--accent-dark));
            box-shadow:0 4px 12px rgba(251,146,60,.35);
        }
        .btn-close-day:hover{ transform:translateY(-2px); }

        /* KPI Grid */
        .kpi-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(230px, 1fr));
            gap:var(--gap-md);
            margin-bottom:var(--gap-lg);
        }
        .kpi-card{
            background:#fff;
            padding:22px;
            border-radius:var(--radius-lg);
            display:flex;
            align-items:center;
            gap:18px;
            box-shadow:var(--shadow-md);
            transition:transform .3s, box-shadow .3s;
        }
        .kpi-card:hover{ transform:translateY(-5px); box-shadow:0 12px 30px rgba(0,0,0,.1); }

        .kpi-icon{
            width:58px; height:58px;
            border-radius:16px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:24px;
            background:var(--accent-soft);
            color:var(--accent-dark);
            flex-shrink:0;
        }
        .kpi-info span{ font-size:13px; color:var(--gray-500); font-weight:600; }
        .kpi-info h2{ margin:4px 0; font-size:21px; color:var(--dark-900); }
        .kpi-info small{ font-size:11px; color:var(--success); font-weight:700; }

        /* Secondary Grid (Objectif & Météo) */
        .secondary-grid{
            display:grid;
            grid-template-columns:2fr 1fr;
            gap:var(--gap-md);
            margin-bottom:var(--gap-lg);
        }
        @media(max-width:900px){ .secondary-grid{ grid-template-columns:1fr; } }

        .goal-box, .weather-box, .section-container, .chart-card{
            background:#fff;
            padding:24px;
            border-radius:var(--radius-lg);
            box-shadow:var(--shadow-md);
        }

        .goal-header{ display:flex; justify-content:space-between; font-weight:700; margin-bottom:12px; }
        .progress-bar-bg{ height:13px; background:var(--gray-200); border-radius:10px; overflow:hidden; margin-bottom:12px; }
        .progress-bar-fill{ height:100%; background:linear-gradient(90deg, var(--success), var(--success-dark)); border-radius:10px; transition:width .6s ease; }
        .goal-footer{ display:flex; justify-content:space-between; font-size:12px; color:var(--gray-500); font-weight:600; }

        .weather-box{
            display:flex;
            align-items:center;
            gap:20px;
            background:linear-gradient(135deg, #eff6ff, #dbeafe);
        }
        .w-icon{ font-size:42px; }
        .weather-box h3{ margin:0; font-size:26px; color:#1e3a8a; }
        .weather-box p{ margin:4px 0 0; font-size:13px; color:#1e40af; }

        /* Charts Grid */
        .charts-grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:var(--gap-md);
            margin-bottom:var(--gap-lg);
        }
        @media(max-width:900px){ .charts-grid{ grid-template-columns:1fr; } }
        .chart-card h3{ margin:0 0 14px; font-size:15px; color:var(--dark-900); }

        /* Top Plats Grid */
        .top-plats-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));
            gap:14px;
            margin-top:15px;
        }
        .plat-card-item{
            background:var(--gray-50);
            padding:14px;
            border-radius:var(--radius-md);
            display:flex;
            align-items:center;
            gap:12px;
            border:1px solid var(--gray-100);
        }
        .plat-badge-icon{
            width:44px; height:44px;
            border-radius:12px;
            background:var(--accent-soft);
            color:var(--accent-dark);
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:19px;
            flex-shrink:0;
        }
        .plat-details h4{ margin:0; font-size:14px; color:var(--dark-900); }
        .plat-details small{ color:var(--gray-500); font-size:12px; }

        /* Table Styles */
        .table-responsive{ overflow-x:auto; }
        table{ width:100%; border-collapse:collapse; margin-top:10px; }
        th{ padding:13px 14px; text-align:left; font-size:12.5px; color:var(--gray-500); background:var(--gray-50); border-bottom:2px solid var(--gray-200); white-space:nowrap; }
        td{ padding:13px 14px; border-bottom:1px solid var(--gray-100); font-size:14px; color:var(--dark-700); }
        tbody tr:hover{ background:var(--gray-50); }

        .badge-status{ padding:5px 12px; border-radius:20px; font-size:11px; font-weight:700; display:inline-block; }
        .badge-status.new{ background:#dbeafe; color:#1e40af; }
        .badge-status.prep{ background:#fef3c7; color:#92400e; }
        .badge-status.ready{ background:#dcfce7; color:#166534; }

        .btn-view{
            background:var(--info-dark);
            color:#fff;
            padding:7px 14px;
            border-radius:8px;
            text-decoration:none;
            font-size:12px;
            font-weight:700;
            display:inline-block;
            transition:background .2s;
        }
        .btn-view:hover{ background:#1d4ed8; }

        .live-badge-pulse{
            display:flex; align-items:center; gap:6px;
            font-size:12px; font-weight:700;
            background:#ecfdf5; color:var(--success-dark);
            padding:6px 12px; border-radius:20px;
            border:1px solid #a7f3d0;
        }
        .pulse-dot{ width:8px; height:8px; background:var(--success); border-radius:50%; animation:pulse-anim 1.5s infinite; }

        @keyframes pulse-anim{
            0%{ transform:scale(.95); box-shadow:0 0 0 0 rgba(16,185,129,.7); }
            70%{ transform:scale(1); box-shadow:0 0 0 8px rgba(16,185,129,0); }
            100%{ transform:scale(.95); box-shadow:0 0 0 0 rgba(16,185,129,0); }
        }
*{

box-sizing:border-box;

}


body{

margin:0;

font-family:
'Segoe UI',
sans-serif;

background:#f8fafc;

color:#111827;

}


/* =========================
SIDEBAR
========================= */


.sidebar{

position:fixed;

left:0;

top:0;

height:100vh;

width:270px;

background:
linear-gradient(
180deg,
#111827,
#1f2937
);


color:white;

padding:25px;

z-index:1000;

transition:.3s;

}



.logo{

display:flex;

align-items:center;

gap:12px;

font-size:22px;

font-weight:800;

margin-bottom:40px;

}



.logo i{

color:#fb923c;

font-size:30px;

}





.menu-title{

font-size:12px;

text-transform:uppercase;

color:#9ca3af;

margin:25px 0 10px;

}





.sidebar a{


display:flex;

align-items:center;

gap:15px;


padding:14px 18px;


border-radius:15px;


color:#d1d5db;


text-decoration:none;


margin-bottom:8px;


transition:.3s;


font-weight:600;

}



.sidebar a:hover,


.sidebar a.active{


background:
rgba(255,255,255,.12);


color:white;


transform:translateX(5px);


}




.sidebar a i{

width:22px;

}




.badge{


margin-left:auto;


background:#ef4444;


padding:4px 9px;


border-radius:20px;


font-size:11px;


}





/* =========================
TOPBAR
========================= */


.main{


margin-left:270px;

min-height:100vh;

}





.topbar{


height:80px;


background:white;


display:flex;


align-items:center;


justify-content:space-between;


padding:0 35px;


box-shadow:

0 5px 20px rgba(0,0,0,.05);


}





.search-box{


background:#f3f4f6;


padding:12px 20px;


border-radius:30px;


width:300px;


}



.search-box input{


border:none;


background:none;


outline:none;


width:85%;


}





.top-actions{


display:flex;


align-items:center;


gap:25px;


}



.notification{


position:relative;


font-size:22px;


}



.notification span{


position:absolute;


top:-8px;


right:-10px;


background:#ef4444;


color:white;


width:18px;


height:18px;


border-radius:50%;


font-size:11px;


display:flex;


align-items:center;


justify-content:center;


}





.profile{


display:flex;


align-items:center;


gap:12px;


}



.avatar{


width:45px;


height:45px;


border-radius:50%;


background:#fb923c;


display:flex;


align-items:center;


justify-content:center;


color:white;


font-weight:bold;


}




/* =========================
CONTENT
========================= */


.content{


padding:35px;


}





/* =========================
MOBILE
========================= */


.menu-toggle{


display:none;


font-size:25px;


cursor:pointer;


}



@media(max-width:900px){


.sidebar{


left:-280px;


}


.sidebar.show{


left:0;


}


.main{


margin-left:0;


}


.menu-toggle{


display:block;


}


.search-box{


display:none;


}


}


    .dashboard-wrapper {
        padding: 10px;
        font-family: 'Segoe UI', sans-serif;
        color: #1f2937;
    }

    .dash-header-card {
        background: linear-gradient(135deg, #111827, #1f2937);
        color: white;
        padding: 30px;
        border-radius: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .dash-header-card h1 { margin: 0; font-size: 28px; }
    .dash-header-card p { margin: 5px 0 0; color: #9ca3af; font-size: 14px; }

    .dash-actions {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .clock-badge {
        background: rgba(255,255,255,0.1);
        padding: 10px 18px;
        border-radius: 15px;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-refresh {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 11px 20px;
        border-radius: 15px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
    }
    .btn-refresh:hover { background: #2563eb; transform: translateY(-2px); }

    .btn-close-day {
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        border: none;
        color: white;
        padding: 11px 20px;
        border-radius: 15px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
    }
    .btn-close-day:hover { transform: translateY(-2px); }

    /* KPI Grid */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .kpi-card {
        background: white;
        padding: 22px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        gap: 18px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.05);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .kpi-card:hover { transform: translateY(-5px); box-shadow: 0 12px 30px rgba(0,0,0,0.08); }

    .kpi-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        background: #f3f4f6;
    }

    .kpi-info span { font-size: 13px; color: #6b7280; font-weight: 600; }
    .kpi-info h2 { margin: 4px 0; font-size: 22px; color: #111827; }
    .kpi-info small { font-size: 11px; color: #10b981; font-weight: bold; }

    /* Secondary Grid (Goal & Weather) */
    .secondary-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }
    @media(max-width: 900px) { .secondary-grid { grid-template-columns: 1fr; } }

    .goal-box, .weather-box, .section-container, .chart-card {
        background: white;
        padding: 25px;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.05);
    }

    .goal-header { display: flex; justify-content: space-between; font-weight: bold; margin-bottom: 12px; }
    .progress-bar-bg { height: 14px; background: #e5e7eb; border-radius: 10px; overflow: hidden; margin-bottom: 12px; }
    .progress-bar-fill { height: 100%; background: linear-gradient(90deg, #10b981, #059669); border-radius: 10px; transition: width 0.6s ease; }
    .goal-footer { display: flex; justify-content: space-between; font-size: 12px; color: #6b7280; font-weight: 600; }

    .weather-box {
        display: flex;
        align-items: center;
        gap: 20px;
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
    }
    .w-icon { font-size: 45px; }
    .weather-box h3 { margin: 0; font-size: 28px; color: #1e3a8a; }
    .weather-box p { margin: 4px 0 0; font-size: 13px; color: #1e40af; }

    /* Charts Grid */
    .charts-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }
    @media(max-width: 900px) { .charts-grid { grid-template-columns: 1fr; } }

    /* Top Plats Grid */
    .top-plats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    .plat-card-item {
        background: #f9fafb;
        padding: 15px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        gap: 12px;
        border: 1px solid #f3f4f6;
    }
    .plat-badge-icon {
        width: 45px; height: 45px; border-radius: 12px; background: #fff7ed; display: flex; align-items: center; justify-content: center; font-size: 20px;
    }
    .plat-details h4 { margin: 0; font-size: 14px; color: #111827; }
    .plat-details small { color: #6b7280; font-size: 12px; }

    /* Table Styles */
    .table-responsive { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th { padding: 14px; text-align: left; font-size: 13px; color: #6b7280; background: #f9fafb; border-bottom: 2px solid #e5e7eb; }
    td { padding: 14px; border-bottom: 1px solid #f3f4f6; font-size: 14px; color: #374151; }

    .badge-status { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; display: inline-block; }
    .badge-status.new { background: #dbeafe; color: #1e40af; }
    .badge-status.prep { background: #fef3c7; color: #92400e; }
    .badge-status.ready { background: #dcfce7; color: #166534; }

    .btn-view {
        background: #2563eb; color: white; padding: 7px 14px; border-radius: 8px; text-decoration: none; font-size: 12px; font-weight: 600; display: inline-block; transition: background 0.2s;
    }
    .btn-view:hover { background: #1d4ed8; }

    .live-badge-pulse {
        display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: bold; background: #ecfdf5; color: #059669; padding: 6px 12px; border-radius: 20px; border: 1px solid #a7f3d0;
    }
    .pulse-dot { width: 8px; height: 8px; background: #10b981; border-radius: 50%; animation: pulse-anim 1.5s infinite; }
    @keyframes pulse-anim {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    /* ===========================
        MAIN
=========================== */

.main{

    margin-left:260px;

    min-height:100vh;

    background:#f8fafc;

}




/* ===========================
        TOPBAR
=========================== */


.topbar{


    height:80px;

    background:white;

    display:flex;

    align-items:center;

    justify-content:space-between;

    padding:0 30px;

    border-bottom:1px solid #e5e7eb;

    position:sticky;

    top:0;

    z-index:100;



}




/* MENU */

.menu-toggle{

    display:none;

    border:none;

    background:#f59e0b;

    color:white;

    width:42px;

    height:42px;

    border-radius:12px;

    cursor:pointer;

    font-size:20px;

}





/* SEARCH */


.search-box{


    width:400px;

    height:45px;

    background:#f8fafc;

    border:1px solid #e5e7eb;

    border-radius:14px;

    display:flex;

    align-items:center;

    gap:12px;

    padding:0 15px;


}



.search-box i{

    color:#9ca3af;

}



.search-box input{

    border:none;

    outline:none;

    background:none;

    width:100%;

    font-size:14px;

}






/* ACTIONS */


.top-actions{

    display:flex;

    align-items:center;

    gap:25px;

}



/* NOTIFICATION */


.notification{


    width:45px;

    height:45px;

    border-radius:50%;

    background:#fff7ed;

    color:#d97706;

    display:flex;

    justify-content:center;

    align-items:center;

    position:relative;

    text-decoration:none;

    font-size:20px;


}



.notification span{


    position:absolute;

    top:-5px;

    right:-5px;


    background:#ef4444;

    color:white;


    width:20px;

    height:20px;


    border-radius:50%;


    display:flex;

    justify-content:center;

    align-items:center;


    font-size:11px;

    font-weight:bold;


}





/* PROFILE */


.profile{


    display:flex;

    align-items:center;

    gap:12px;

    cursor:pointer;

}



.avatar{


    width:45px;

    height:45px;

    border-radius:50%;


    background:linear-gradient(135deg,#f59e0b,#d97706);


    color:white;


    display:flex;


    align-items:center;


    justify-content:center;


    font-weight:900;


    font-size:20px;


}



.profile-info strong{

    display:block;

    color:#1f2937;

    font-size:14px;

}



.profile-info small{


    color:#6b7280;

    font-size:12px;


}



.arrow{


    color:#9ca3af;

    font-size:12px;


}





/* CONTENT */


.content{

    padding:30px;

}




/* ===========================
        MOBILE
=========================== */


@media(max-width:900px){


.main{

    margin-left:0;

}



.menu-toggle{

    display:block;

}



.search-box{

    display:none;

}



.topbar{

    padding:0 15px;

}



.profile-info,
.arrow{

    display:none;

}



.content{

    padding:15px;

}


}
</style>


</head>


<body>


<div class="sidebar" id="sidebar">

    <div class="sidebar-top">
        <div class="logo">
            <i class="fa-solid fa-utensils"></i>
            RESTO KAY-Y
        </div>

        <div class="menu-title">Principal</div>
        <nav>
            <a href="/admin/dashboard" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>
        </nav>

        <div class="menu-title">Restaurant</div>
        <nav>
            <a href="/admin/plats" class="{{ request()->is('admin/plats*') ? 'active' : '' }}">
                <i class="fa-solid fa-burger"></i> Menu
            </a>
            <a href="/admin/categories" class="{{ request()->is('admin/categories*') ? 'active' : '' }}">
                <i class="fa-solid fa-folder"></i> Catégories
            </a>
            <a href="/admin/tables" class="{{ request()->is('admin/tables*') ? 'active' : '' }}">
                <i class="fa-solid fa-chair"></i> Tables
            </a>
           <a href="/cuisine" class="{{ request()->is('cuisine*') ? 'active' : '' }}">

    <i class="fa-solid fa-fire"></i>

    <span>
        Cuisine
    </span>

    @if($commandesCuisine > 0)
        <span class="badge">
            {{ $commandesCuisine }}
        </span>
    @endif

</a>
        </nav>

        <div class="menu-title">Finance</div>
        <nav>
            <a href="/admin/ventes" class="{{ request()->is('admin/ventes*') ? 'active' : '' }}">
                <i class="fa-solid fa-money-bill"></i> Ventes
            </a>
            <a href="/admin/employes" class="{{ request()->is('admin/employes*') ? 'active' : '' }}">
                <i class="fa-solid fa-users-gear"></i> Employés
            </a>
            <a href="/admin/stock" class="{{ request()->is('admin/stock*') ? 'active' : '' }}">
                <i class="fa-solid fa-boxes-stacked"></i> Stocks &amp; Alertes
                @if(isset($stockAlertsCount) && $stockAlertsCount > 0)
                    <span class="badge pulse">{{ $stockAlertsCount }}</span>
                @endif
            </a>
            <a href="/admin/suppliers" class="{{ request()->is('admin/suppliers*') ? 'active' : '' }}">
                <i class="fa-solid fa-truck-field"></i> Fournisseurs
            </a>
        </nav>

        <div class="menu-title">Système</div>
        <nav>
            <a href="/admin/notifications" class="{{ request()->is('admin/notifications*') ? 'active' : '' }}">
                <i class="fa-solid fa-bell"></i> Notifications &amp; Rappels
                @if(isset($stockAlertsCount) && $stockAlertsCount > 0)
                    <span class="badge pulse">{{ $stockAlertsCount }}</span>
                @endif
            </a>
            <a href="/admin/users" class="{{ request()->is('admin/users*') ? 'active' : '' }}">
                <i class="fa-solid fa-users-gear"></i> Utilisateurs &amp; Rôles
            </a>
            <a href="/admin/reports" class="{{ request()->is('admin/reports*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> Rapports &amp; Ventes
            </a>
            <a href="/admin/profile" class="{{ request()->is('admin/profile*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-gear"></i> Mon Profil
            </a>
        </nav>
    </div>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
            </button>
        </form>
    </div>

</div>





<!-- MAIN -->
<div class="main">


    <!-- ==========================
            TOPBAR
    =========================== -->

    <header class="topbar">


        <!-- MENU MOBILE -->

        <button class="menu-toggle" onclick="toggleMenu()">

            <i class="fa-solid fa-bars"></i>

        </button>




        <!-- SEARCH -->

        <div class="search-box">

            <i class="fa-solid fa-magnifying-glass"></i>

            <input type="text"
                   placeholder="Rechercher dans le système...">

        </div>





        <!-- ACTIONS -->

        <div class="top-actions">



            <!-- NOTIFICATION -->

           <a href="/admin/notifications" class="notification">


                <i class="fa-solid fa-bell"></i>


                @if(isset($notificationCount) && $notificationCount > 0)

                <span>
                    {{ $notificationCount }}
                </span>

                @else

                <span>
                    0
                </span>

                @endif


            </a>







            <!-- PROFILE -->


            <div class="profile-dropdown">


                <div class="profile">


                    <div class="avatar">


                        @auth

                        {{ strtoupper(substr(auth()->user()->name,0,1)) }}

                        @else

                        A

                        @endauth


                    </div>





                    <div class="profile-info">


                        @auth

                        <strong>
                            {{ auth()->user()->name }}
                        </strong>


                        <small>
                            {{ ucfirst(auth()->user()->role ?? 'Admin') }}
                        </small>


                        @else


                        <strong>
                            Admin
                        </strong>


                        <small>
                            Restaurant
                        </small>


                        @endauth



                    </div>


                    <i class="fa-solid fa-chevron-down arrow"></i>


                </div>



            </div>




        </div>


    </header>






    <!-- ==========================
            PAGE CONTENT
    =========================== -->


    <main class="content">

        @yield('content')

    </main>



</div>




<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Transfè done Laravel yo bay JavaScript la
    window.salesChartData = @json($salesChart);
    window.orderStatsData = {
        completed: @json($completedOrders),
        preparing: @json($preparingOrders),
        new: @json($newOrders)
    };
</script>
<script>
    function toggleMenu() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.classList.toggle('show');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // 🕒 Realtime Clock
        function updateClock() {
            const now = new Date();
            const clockElement = document.getElementById('liveClock');
            if (clockElement) {
                clockElement.innerText = now.toLocaleTimeString();
            }
        }
        setInterval(updateClock, 1000);
        updateClock();

        // 📈 Chart.js - Sales Week
        const ctxSalesElement = document.getElementById('salesWeekChart');
        if (ctxSalesElement) {
            if (window.salesChartData && Array.isArray(window.salesChartData) && window.salesChartData.length > 0) {
                new Chart(ctxSalesElement.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: window.salesChartData.map(item => item.date),
                        datasets: [{
                            label: 'Ventes (HTG)',
                            data: window.salesChartData.map(item => item.total),
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
                });
            } else {
                // Done vid yo tès: kreye yon graf vid pou w wè si l desine
                new Chart(ctxSalesElement.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: ['Lendi', 'Madi', 'Mèkredi', 'Jedi', 'Vandredi', 'Samdi', 'Dimanch'],
                        datasets: [{
                            label: 'Ventes (HTG)',
                            data: [0, 0, 0, 0, 0, 0, 0],
                            borderColor: '#2563eb',
                            fill: true
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            }
        }

        // 🍩 Chart.js - Order Status Doughnut
        const ctxStatusElement = document.getElementById('orderStatusChart');
        if (ctxStatusElement) {
            const stats = window.orderStatsData || { completed: 0, preparing: 0, new: 0 };
            new Chart(ctxStatusElement.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Prêtes', 'En Préparation', 'Nouvelles'],
                    datasets: [{
                        data: [stats.completed || 0, stats.preparing || 0, stats.new || 0],
                        backgroundColor: ['#10b981', '#f59e0b', '#3b82f6'],
                        borderWidth: 2
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
            });
        }

        // 🔔 Pusher & Laravel Echo Real-time Notifications Setup
        const sound = document.getElementById('notifSound');
        let unlocked = false;

        document.body.addEventListener('click', () => {
            unlocked = true;
            if (sound) {
                sound.play().then(() => { sound.pause(); sound.currentTime = 0; }).catch(()=>{});
            }
        }, { once: true });

        function playSound() {
            if (!unlocked || !sound) return;
            sound.currentTime = 0;
            sound.play().catch(()=>{});
        }

        function initEcho() {
            if (!window.Echo) {
                setTimeout(initEcho, 500);
                return;
            }

            const channel = window.Echo.channel('kitchen');

            channel.listen('.new-order', (e) => {
                playSound();
                const tableBody = document.getElementById('order-list-table');
                if (!tableBody) return;

                const newRow = `
                    <tr id="order-row-${e.commande.id}" style="background: #eff6ff; transition: background 1s;">
                        <td><strong>#${e.commande.id}</strong></td>
                        <td>Table ${e.commande.table ? e.commande.table.numero : 'N/A'}</td>
                        <td>${e.commande.client || 'Client standard'}</td>
                        <td><strong>${parseFloat(e.commande.total).toFixed(2)} HTG</strong></td>
                        <td><span class="badge-status new">Nouvelle</span></td>
                        <td>A l'instant</td>
                        <td>
                            <a href="/facture/${e.commande.id}" class="btn-view">👁 Voir Facture</a>
                        </td>
                    </tr>
                `;
                tableBody.insertAdjacentHTML('afterbegin', newRow);
            });

            channel.listen('.ready', (e) => {
                playSound();
                const row = document.getElementById('order-row-' + e.commande.id);
                if (row) {
                    const badge = row.querySelector('.badge-status');
                    if(badge) {
                        badge.className = 'badge-status ready';
                        badge.innerText = 'Prête';
                    }
                }
            });

            channel.listen('.accepted', (e) => {
                playSound();
                const row = document.getElementById('order-row-' + e.commande.id);
                if (row) {
                    const badge = row.querySelector('.badge-status');
                    if(badge) {
                        badge.className = 'badge-status prep';
                        badge.innerText = 'En préparation';
                    }
                }
            });
        }

        initEcho();

        // ☀️ Real-time Weather - Les Cayes
        const weatherTemp = document.getElementById('weatherTemp');
        if (weatherTemp) {
            fetch('https://api.open-meteo.com/v1/forecast?latitude=18.1965&longitude=-73.7442&current=temperature_2m')
                .then(response => response.json())
                .then(data => {
                    if (data && data.current) {
                        const temp = Math.round(data.current.temperature_2m);
                        weatherTemp.innerText = temp + '°C';
                    }
                })
                .catch(error => console.error('Erreur météo:', error));
        }
    });
</script>



</body>

</html>