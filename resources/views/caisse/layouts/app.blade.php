<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title','Caisse') — Resto Kay-Y</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=IBM+Plex+Mono:wght@400;500;600&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
    :root{
        --espresso: #3C2415;
        --espresso-light: #5C4033;
        --mocha: #6F4E37;
        --brass: #B87333;
        --brass-light: #D4AF37;
        --gold: #C9A227;
        --paprika: #E25822;
        --paprika-dark: #B7410E;
        --terracotta: #C1440E;
        --cream: #FAF7F2;
        --cream-dark: #F0EBE3;
        --linen: #E8E0D5;
        --ink: #2B2118;
        --ink-muted: #8C7B6B;

        --sidebar-bg: linear-gradient(180deg, #2C1810 0%, #3C2415 60%, #4A2C1A 100%);
        --card-bg: #FFFFFF;
        --page-bg: #F5F1E8;
        --success: #4A7C59;
        --danger: #C0392B;

        --font-bistro: 'Playfair Display', serif;
        --font-mono: 'IBM Plex Mono', monospace;
        --font-body: 'Inter', sans-serif;
    }

    *{ margin:0; padding:0; box-sizing:border-box; }
    body{
        font-family: var(--font-body);
        background: var(--page-bg);
        color: var(--ink);
        display: flex;
        min-height: 100vh;
        overflow-x: hidden;
    }

    .sidebar{
        position: fixed; left: 0; top: 0;
        width: 270px; height: 100%;
        background: var(--sidebar-bg);
        color: #F5F1E8;
        padding: 28px 22px;
        display: flex; flex-direction: column;
        z-index: 999;
        box-shadow: 10px 0 40px rgba(44,24,16,.35);
    }
    .logo{ display: flex; align-items: center; gap: 14px; margin-bottom: 45px; }
    .logo-icon{
        width: 56px; height: 56px; border-radius: 16px;
        background: linear-gradient(135deg, var(--brass), var(--paprika));
        display: flex; align-items: center; justify-content: center;
        font-size: 26px; color: #fff;
        box-shadow: 0 8px 20px rgba(184,115,51,.35);
        border: 2px solid rgba(255,255,255,.15);
    }
    .logo-text h2{
        font-family: var(--font-bistro); font-size: 24px; font-weight: 800;
        letter-spacing: 1px; color: #FFF8E7;
    }
    .logo-text span{
        font-size: 12px; color: var(--brass-light);
        text-transform: uppercase; letter-spacing: 2px; font-weight: 500;
    }
    .menu{ display: flex; flex-direction: column; gap: 8px; margin-top: 10px; }
    .menu a{
        display: flex; align-items: center; gap: 14px;
        padding: 14px 16px; text-decoration: none; color: #D7C4B2;
        border-radius: 14px; transition: .35s; font-size: 14px; font-weight: 500;
        border: 1px solid transparent;
    }
    .menu a:hover{
        background: rgba(255,255,255,.06); color: #FFF;
        border-color: rgba(184,115,51,.3); transform: translateX(4px);
    }
    .menu a.active{
        background: linear-gradient(135deg, rgba(184,115,51,.25), rgba(226,88,34,.2));
        color: #FFF; border: 1px solid rgba(184,115,51,.4);
        box-shadow: 0 8px 20px rgba(0,0,0,.2);
    }
    .menu i{ width: 22px; font-size: 17px; text-align: center; }

    .sidebar-bottom{ margin-top: auto; padding-top: 20px; border-top: 1px solid rgba(255,255,255,.1); }
    .logout{
        background: linear-gradient(135deg, var(--paprika-dark), var(--paprika))!important;
        color: #fff!important; border: none!important;
    }
    .logout:hover{ filter: brightness(1.15); transform: translateX(4px)!important; }

    .main{ margin-left: 270px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
    .topbar{
        height: 80px; background: var(--cream);
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 35px; box-shadow: 0 4px 20px rgba(44,24,16,.06);
        position: sticky; top: 0; z-index: 100;
        border-bottom: 1px solid var(--linen);
    }
    .left-top{ display: flex; align-items: center; gap: 18px; }
    .left-top h1{
        font-family: var(--font-bistro); font-size: 26px; font-weight: 800; color: var(--espresso);
    }
    .left-top small{ display: block; color: var(--ink-muted); font-size: 13px; font-weight: 500; }
    .right-top{ display: flex; align-items: center; gap: 18px; }

    .search-box{ position: relative; }
    .search-box input{
        width: 300px; padding: 11px 18px 11px 42px;
        border: 1px solid var(--linen); outline: none; background: #fff;
        border-radius: 40px; font-size: 13px; transition: .3s; font-family: var(--font-body);
    }
    .search-box input:focus{ border-color: var(--brass); box-shadow: 0 0 0 3px rgba(184,115,51,.12); }
    .search-box i{ position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--ink-muted); }
    .sr-only{ position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0; }

    .icon-btn{
        position: relative; width: 44px; height: 44px; border-radius: 50%;
        border: 1px solid var(--linen); display: flex; align-items: center; justify-content: center;
        background: #fff; cursor: pointer; font-size: 18px; color: var(--espresso); transition: .35s;
    }
    .icon-btn:hover{ background: var(--espresso); color: var(--brass-light); border-color: var(--espresso); transform: translateY(-2px); }
    .badge{
        position: absolute; top: 2px; right: 2px; width: 17px; height: 17px; border-radius: 50%;
        background: var(--paprika); color: white; font-size: 10px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-family: var(--font-mono);
    }
    .user{ display: flex; align-items: center; gap: 12px; padding-left: 18px; border-left: 1px solid var(--linen); }
    .avatar{
        width: 46px; height: 46px; border-radius: 50%;
        background: linear-gradient(135deg, var(--brass), var(--paprika));
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 18px; color: white;
        box-shadow: 0 6px 14px rgba(184,115,51,.25);
        font-family: var(--font-bistro);
    }
    .user h3{ font-size: 15px; color: var(--espresso); font-weight: 600; }
    .user small{ display: block; color: var(--ink-muted); margin-top: 2px; font-size: 12px; }

    .page{ padding: 30px 35px; flex: 1; }
    .footer{
        background: var(--cream); padding: 16px; text-align: center;
        color: var(--ink-muted); font-size: 13px; border-top: 1px solid var(--linen);
        font-family: var(--font-mono); letter-spacing: .5px;
    }
    .mobile-toggle{ display: none; font-size: 26px; cursor: pointer; background: none; border: none; color: var(--espresso); padding: 0; }

    /* ====== CAISSE COMPONENTS ====== */
    .caisse-container{ padding: 10px 5px; }
    .caisse-title{ font-family: var(--font-bistro); font-size: 32px; font-weight: 800; color: var(--espresso); letter-spacing: .5px; }
    .caisse-subtitle{ color: var(--ink-muted); margin-top: 6px; font-size: 15px; }

    .welcome-card{
        background: linear-gradient(135deg, var(--espresso) 0%, var(--mocha) 100%);
        color: #fff; border-radius: 24px; padding: 35px;
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 30px; box-shadow: 0 15px 35px rgba(44,24,16,.2);
        border: 1px solid rgba(184,115,51,.25); position: relative; overflow: hidden;
    }
    .welcome-card::before{
        content: ''; position: absolute; top: -50%; right: -10%;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(184,115,51,.2) 0%, transparent 70%); border-radius: 50%;
    }
    .welcome-card h2{ font-family: var(--font-bistro); font-size: 28px; font-weight: 700; margin-bottom: 8px; }
    .welcome-card p{ color: #D7C4B2; font-size: 15px; max-width: 500px; line-height: 1.5; }
    .welcome-icon{ font-size: 52px; color: var(--brass-light); opacity: .9; z-index: 1; }

    .stats-grid{ display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 22px; margin-bottom: 30px; }
    .stat-card{
        background: var(--card-bg); border-radius: 22px; padding: 24px;
        display: flex; justify-content: space-between; align-items: center;
        box-shadow: 0 8px 25px rgba(44,24,16,.06); transition: .35s;
        position: relative; overflow: hidden; border: 1px solid var(--linen);
    }
    .stat-card:hover{ transform: translateY(-5px); box-shadow: 0 14px 35px rgba(44,24,16,.1); }
    .stat-card .icon{
        width: 52px; height: 52px; border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; color: #fff;
    }
    .stat-card.revenue .icon{ background: linear-gradient(135deg, var(--brass), var(--gold)); }
    .stat-card.orders .icon{ background: linear-gradient(135deg, var(--paprika), var(--terracotta)); }
    .stat-card.attente .icon{ background: linear-gradient(135deg, #8C7B6B, var(--ink-muted)); }
    .stat-card.paiement .icon{ background: linear-gradient(135deg, var(--success), #3D6B4F); }

    .stat-card span{ color: var(--ink-muted); font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: .8px; }
    .stat-card h2{ margin: 6px 0; font-size: 26px; font-weight: 700; font-family: var(--font-mono); color: var(--espresso); }
    .stat-card small{ color: var(--ink-muted); font-size: 12px; }

    /* TICKET PERFORÉ */
    .ticket-perforated{ position: relative; }
    .ticket-perforated::before,
    .ticket-perforated::after{
        content: ''; position: absolute; left: 0; width: 100%; height: 10px;
        background-image: radial-gradient(circle at 6px 6px, var(--page-bg) 6px, transparent 7px);
        background-size: 18px 18px; background-repeat: repeat-x; z-index: 2;
    }
    .ticket-perforated::before{ top: -5px; transform: rotate(180deg); }
    .ticket-perforated::after{ bottom: -5px; }

    .analytics-grid{ display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px; }
    .analytics-grid.two{ grid-template-columns: repeat(2, 1fr); }
    .analytics-grid.two-columns{ grid-template-columns: 1.2fr 1fr; }

    .chart-card, .table-card{
        background: var(--card-bg); border-radius: 24px; padding: 28px;
        box-shadow: 0 8px 25px rgba(44,24,16,.06); border: 1px solid var(--linen);
    }
    /* ====== FIX HAUTEUR GRAPHIQUES ====== */
    .chart-card{ position: relative; min-height: 320px; }
    .chart-card canvas{ max-height: 260px !important; width: 100% !important; }

    .card-header{ display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; flex-wrap: wrap; gap: 12px; }
    .card-header h3{ margin: 0; font-family: var(--font-bistro); font-size: 20px; font-weight: 700; color: var(--espresso); }
    .card-header i{ color: var(--brass); margin-right: 8px; font-size: 18px; }
    .card-header small{ color: var(--ink-muted); font-size: 13px; display: block; margin-top: 4px; font-family: var(--font-body); }
    .count-badge{
        background: linear-gradient(135deg, var(--paprika), var(--terracotta));
        color: #fff; padding: 6px 14px; border-radius: 30px;
        font-size: 12px; font-weight: 700; font-family: var(--font-mono);
    }

    .mini-stats{ display: flex; flex-direction: column; gap: 16px; }
    .mini-card{
        background: var(--card-bg); border-radius: 20px; padding: 22px;
        display: flex; justify-content: space-between; align-items: center;
        box-shadow: 0 6px 20px rgba(44,24,16,.06); border: 1px solid var(--linen); transition: .3s;
    }
    .mini-card:hover{ transform: translateX(5px); }
    .mini-card small{ color: var(--ink-muted); font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
    .mini-card h2{ font-family: var(--font-mono); font-size: 22px; color: var(--espresso); margin-top: 4px; }
    .mini-card i{ font-size: 28px; opacity: .35; }
    .mini-card.success i{ color: var(--success); }
    .mini-card.primary i{ color: var(--brass); }
    .mini-card.warning i{ color: var(--paprika); }
    .mini-card.purple i{ color: var(--espresso-light); }

    .table-responsive{ overflow-x: auto; }
    .premium-table{ width: 100%; border-collapse: collapse; }
    .premium-table thead{ background: var(--cream-dark); }
    .premium-table th{
        text-align: left; padding: 14px 16px; font-size: 11px;
        text-transform: uppercase; color: var(--ink-muted); letter-spacing: 1px;
        font-weight: 700; font-family: var(--font-body);
    }
    .premium-table td{ padding: 16px; border-bottom: 1px solid var(--linen); color: var(--ink); font-size: 14px; }
    .premium-table tbody tr{ transition: .25s; }
    .premium-table tbody tr:hover{ background: var(--cream); }

    .amount{ font-family: var(--font-mono); color: var(--success); font-size: 16px; font-weight: 600; letter-spacing: .5px; }
    .amount-lg{ font-family: var(--font-mono); font-size: 28px; font-weight: 700; color: var(--espresso); }

    .table-number{
        background: var(--cream-dark); color: var(--espresso);
        padding: 8px 14px; border-radius: 12px; font-weight: 700;
        display: inline-flex; align-items: center; gap: 6px;
        font-family: var(--font-mono); font-size: 13px; border: 1px dashed var(--linen);
    }

    .badge-ready{
        background: #E8F5E9; color: #2E7D32; padding: 7px 14px; border-radius: 30px;
        font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;
        font-family: var(--font-body);
    }
    .badge-mode{
        background: var(--cream-dark); color: var(--ink-muted); padding: 7px 14px;
        border-radius: 30px; font-size: 12px; font-weight: 600;
        font-family: var(--font-mono); border: 1px solid var(--linen);
    }
    .badge{ padding: 6px 12px; border-radius: 30px; font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; gap: 5px; }
    .badge-success{ background: #E8F5E9; color: #2E7D32; }
    .badge-primary{ background: #FFF3E0; color: #E65100; }
    .badge-warning{ background: #FFF8E1; color: #F9A825; }
    .badge-purple{ background: #F3E5F5; color: #6A1B9A; }

    .btn-pay{
        background: linear-gradient(135deg, var(--success), #3D6B4F); color: white;
        padding: 10px 18px; border-radius: 14px; text-decoration: none;
        display: inline-flex; align-items: center; gap: 8px;
        font-weight: 700; font-size: 13px; transition: .3s;
        box-shadow: 0 6px 18px rgba(74,124,89,.25);
        border: none; cursor: pointer; font-family: var(--font-body);
    }
    .btn-pay:hover{
        transform: translateY(-3px); color: white;
        box-shadow: 0 10px 25px rgba(74,124,89,.35); filter: brightness(1.05);
    }

    .payment-summary{ display: flex; flex-direction: column; gap: 12px; }
    .payment-item{
        display: flex; justify-content: space-between; align-items: center;
        padding: 16px; background: var(--cream); border-radius: 16px;
        transition: .3s; border: 1px solid transparent;
    }
    .payment-item:hover{
        background: #fff; border-color: var(--linen);
        transform: translateX(5px); box-shadow: 0 4px 12px rgba(44,24,16,.06);
    }
    .payment-item strong{ font-size: 20px; color: var(--espresso); font-family: var(--font-mono); }
    .payment-item .left{ display: flex; align-items: center; gap: 10px; font-weight: 600; color: var(--ink); font-size: 14px; }

    .empty{ text-align: center; padding: 45px; color: var(--ink-muted); font-weight: 600; font-size: 15px; }

    .caisse-footer{
        margin-top: 35px; padding: 22px 28px; background: var(--card-bg);
        border-radius: 22px; display: flex; justify-content: space-between; align-items: center;
        color: var(--ink-muted); font-size: 13px;
        box-shadow: 0 8px 25px rgba(44,24,16,.06); border: 1px solid var(--linen);
        font-family: var(--font-mono);
    }
    .caisse-footer div{ display: flex; align-items: center; gap: 10px; }
    .caisse-footer i{ color: var(--brass); }

    #toastContainer{ position: fixed; top: 25px; right: 25px; z-index: 9999; display: flex; flex-direction: column; gap: 14px; }
    .toast{
        min-width: 320px; padding: 18px 22px; border-radius: 18px; background: var(--card-bg);
        display: flex; align-items: center; gap: 14px; font-weight: 700;
        box-shadow: 0 15px 40px rgba(44,24,16,.15); animation: slideIn .4s ease;
        border: 1px solid var(--linen); font-family: var(--font-body);
    }
    .toast.success{ border-left: 5px solid var(--success); color: var(--success); }
    .toast.error{ border-left: 5px solid var(--danger); color: var(--danger); }
    @keyframes slideIn{ from{ opacity:0; transform: translateX(100px); } to{ opacity:1; transform: translateX(0); } }

    .paiement-box{ max-width: 900px; margin: 0 auto; }
    .commande-resume{ display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin: 28px 0; }
    .resume-card{
        background: var(--cream); padding: 24px; border-radius: 20px;
        display: flex; flex-direction: column; gap: 10px;
        border: 1px solid var(--linen); transition: .3s;
    }
    .resume-card:hover{ transform: translateY(-3px); box-shadow: 0 8px 20px rgba(44,24,16,.06); }
    .resume-card span{ color: var(--ink-muted); font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: .8px; }
    .resume-card strong{ font-size: 24px; color: var(--espresso); font-family: var(--font-mono); }
    .resume-card .amount{ font-size: 24px; }

    .payment-title{ margin: 28px 0 18px; font-family: var(--font-bistro); font-size: 22px; color: var(--espresso); font-weight: 700; }
    .payment-methods{ display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; }
    .method-card{ cursor: pointer; }
    .method-card input{ display: none; }
    .method-card div{
        padding: 24px 16px; background: var(--cream); border-radius: 20px; text-align: center;
        transition: .3s; border: 2px solid transparent; height: 100%;
        display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px;
    }
    .method-card i{ display: block; font-size: 28px; margin-bottom: 4px; color: var(--brass); transition: .3s; }
    .method-card span{ font-size: 13px; font-weight: 600; color: var(--ink); }
    .method-card:hover div{ transform: translateY(-5px); background: #fff; border-color: var(--linen); box-shadow: 0 8px 20px rgba(44,24,16,.06); }
    .method-card input:checked + div{
        border-color: var(--brass);
        background: linear-gradient(135deg, #FFF8E1, #FFF3E0);
        box-shadow: 0 8px 20px rgba(184,115,51,.15);
    }
    .method-card input:checked + div i{ color: var(--paprika); }
    .validate-pay{ margin-top: 32px; width: 100%; justify-content: center; font-size: 16px; padding: 16px; border-radius: 18px; }

    .facture-card{
        background: var(--card-bg); max-width: 800px; margin: auto; padding: 40px;
        border-radius: 28px; box-shadow: 0 15px 40px rgba(44,24,16,.1); border: 1px solid var(--linen);
    }
    .facture-header{ display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .facture-header h1{ margin: 0; font-family: var(--font-bistro); font-size: 36px; color: var(--espresso); }
    .facture-header p{ color: var(--ink-muted); margin-top: 6px; font-size: 14px; }
    .facture-number{ text-align: right; display: flex; flex-direction: column; gap: 4px; }
    .facture-number strong{ color: var(--brass); font-size: 20px; font-family: var(--font-mono); }
    .facture-number small{ color: var(--ink-muted); font-size: 12px; }
    .facture-info{ display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin: 25px 0; }
    .facture-info div{ background: var(--cream); padding: 18px; border-radius: 18px; display: flex; flex-direction: column; gap: 6px; border: 1px solid var(--linen); }
    .facture-info span{ color: var(--ink-muted); font-size: 12px; text-transform: uppercase; letter-spacing: .8px; font-weight: 600; }
    .facture-info strong{ font-family: var(--font-mono); color: var(--espresso); font-size: 16px; }
    .facture-table{ width: 100%; border-collapse: collapse; margin-top: 25px; font-size: 14px; }
    .facture-table th{ background: var(--cream-dark); padding: 16px; text-align: left; font-family: var(--font-body); font-weight: 700; color: var(--ink-muted); text-transform: uppercase; font-size: 11px; letter-spacing: 1px; }
    .facture-table td{ padding: 16px; border-bottom: 1px solid var(--linen); }
    .facture-total{ margin-top: 30px; display: flex; justify-content: space-between; align-items: center; padding: 24px; background: var(--cream); border-radius: 20px; border: 1px solid var(--linen); }
    .facture-total h2{ color: var(--espresso); font-family: var(--font-mono); font-size: 28px; }
    .facture-footer{ margin-top: 35px; display: flex; justify-content: space-between; align-items: center; color: var(--ink-muted); font-size: 13px; }

    @media print{ .sidebar, .topbar, .btn-pay{ display: none!important; } .main{ margin-left: 0!important; } .facture-card{ box-shadow: none; border: none; } }

    ::-webkit-scrollbar{ width: 8px; }
    ::-webkit-scrollbar-track{ background: var(--cream); }
    ::-webkit-scrollbar-thumb{ background: var(--linen); border-radius: 20px; }
    ::-webkit-scrollbar-thumb:hover{ background: var(--ink-muted); }

    @media(max-width:1100px){
        .search-box input{ width: 220px; }
        .analytics-grid, .analytics-grid.two, .analytics-grid.two-columns{ grid-template-columns: 1fr; }
    }
    @media(max-width:900px){
        .sidebar{ left: -270px; transition: .35s; } .sidebar.show{ left: 0; }
        .main{ margin-left: 0; } .mobile-toggle{ display: block; }
        .search-box{ display: none; } .topbar{ padding: 0 20px; }
        .page{ padding: 20px; } .commande-resume{ grid-template-columns: 1fr; }
        .payment-methods{ grid-template-columns: repeat(2, 1fr); }
        .facture-info{ grid-template-columns: 1fr; }
    }
    @media(max-width:600px){
        .user h3, .user small{ display: none; } .right-top{ gap: 10px; }
        .payment-methods{ grid-template-columns: 1fr; }
        .caisse-footer{ flex-direction: column; gap: 12px; text-align: center; }
        .welcome-card{ flex-direction: column; text-align: center; gap: 20px; }
        .welcome-card p{ max-width: 100%; } .stats-grid{ grid-template-columns: 1fr; }
        .facture-header{ flex-direction: column; text-align: center; gap: 15px; }
        .facture-total{ flex-direction: column; gap: 10px; text-align: center; }
    }

    .dark-mode{ --page-bg: #1A1410; --card-bg: #241C16; --cream: #2A201A; --cream-dark: #332820; --linen: #3E3028; --ink: #E8DCD4; --ink-muted: #A89080; }
    .dark-mode .sidebar{ box-shadow: 10px 0 40px rgba(0,0,0,.5); }
    .dark-mode .topbar{ background: #241C16; border-color: #3E3028; }
    .dark-mode .welcome-card{ background: linear-gradient(135deg, #1A1410 0%, #2A201A 100%); border-color: #3E3028; }
    .dark-mode .welcome-card h2{ color: #FFF8E7; }
    .dark-mode .premium-table thead{ background: #2A201A; }
    .dark-mode .premium-table tbody tr:hover{ background: #2A201A; }
    .dark-mode .method-card div{ background: #2A201A; }
    .dark-mode .method-card input:checked + div{ background: linear-gradient(135deg, #3E3028, #332820); }
    .dark-mode .footer{ background: #241C16; border-color: #3E3028; }
    .dark-mode .toast{ background: #241C16; border-color: #3E3028; }

    .logout{
    background: linear-gradient(135deg, rgba(60,36,21,.6), rgba(44,24,16,.4)) !important;
    color: #E8DCD4 !important;
    border: 1px solid rgba(184,115,51,.25) !important;
    box-shadow: inset 0 1px 0 rgba(255,255,255,.05), 0 4px 12px rgba(0,0,0,.2);
}

.logout:hover{
    background: linear-gradient(135deg, rgba(226,88,34,.2), rgba(184,115,51,.15)) !important;
    border-color: var(--paprika) !important;
    color: #FFF !important;
    transform: translateX(5px) !important;
}/* =========================================
   SIDEBAR BOTTOM
========================================= */

.sidebar-bottom {
    margin-top: auto;
    padding: 15px;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
}

/* Bouton déconnexion */

.sidebar-bottom .logout {
    display: flex;
    align-items: center;
    gap: 12px;

    width: 100%;
    padding: 12px 15px;

    color: #f87171;
    background: rgba(239, 68, 68, 0.08);

    border: 1px solid rgba(239, 68, 68, 0.12);
    border-radius: 10px;

    text-decoration: none;

    font-size: 14px;
    font-weight: 600;

    transition:
        background 0.25s ease,
        color 0.25s ease,
        transform 0.25s ease,
        box-shadow 0.25s ease;
}

/* Icône */

.sidebar-bottom .logout i {
    width: 20px;

    font-size: 16px;

    text-align: center;

    transition: transform 0.25s ease;
}

/* Hover */

.sidebar-bottom .logout:hover {
    color: #ffffff;

    background: #dc2626;

    border-color: #dc2626;

    transform: translateY(-1px);

    box-shadow: 0 6px 18px rgba(220, 38, 38, 0.25);
}

/* Animation icône */

.sidebar-bottom .logout:hover i {
    transform: translateX(3px);
}

/* Active / clic */

.sidebar-bottom .logout:active {
    transform: scale(0.98);
}

/* Form logout */

.sidebar-bottom form {
    margin: 0;
    padding: 0;
}
    </style>
    <style>

.facture-page {
    max-width: 1150px;
    margin: 0 auto;
    padding: 25px;
}

.facture-navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    gap: 15px;
}

.facture-nav-right {
    display: flex;
    gap: 10px;
}

.facture-nav-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 17px;
    border-radius: 10px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    background: #2c1810;
    color: white;
    font-weight: 600;
    transition: .2s;
}

.facture-nav-btn:hover {
    transform: translateY(-2px);
    opacity: .9;
}

.facture-nav-btn.secondary {
    background: #f3f4f6;
    color: #2c1810;
}

.facture-nav-btn.print-btn {
    background: #b87333;
}

.facture-card {
    background: white;
    border-radius: 18px;
    padding: 35px;
    box-shadow: 0 10px 35px rgba(44,24,16,.10);
}

.facture-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 30px;
}

.restaurant-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.restaurant-logo {
    width: 65px;
    height: 65px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff5eb;
    font-size: 32px;
}

.restaurant-info h1 {
    margin: 0;
    font-size: 28px;
    color: #2c1810;
}

.restaurant-info p {
    margin: 5px 0;
    color: #6b7280;
}

.restaurant-info small {
    color: #9ca3af;
}

.facture-number {
    text-align: right;
}

.facture-number span {
    display: block;
    font-size: 12px;
    color: #9ca3af;
    letter-spacing: 2px;
}

.facture-number strong {
    display: block;
    margin: 5px 0;
    font-size: 22px;
    color: #b87333;
}

.facture-number small {
    color: #6b7280;
}

.facture-divider {
    height: 1px;
    background: #eee;
    margin: 28px 0;
}

.facture-info {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
}

.info-box {
    background: #fafafa;
    border-radius: 12px;
    padding: 15px;
}

.info-box span {
    display: block;
    color: #8a8a8a;
    font-size: 13px;
    margin-bottom: 7px;
}

.info-box strong {
    font-size: 16px;
    color: #2c1810;
}

.facture-section-title {
    margin-top: 30px;
    margin-bottom: 15px;
}

.facture-section-title h3 {
    margin: 0;
    color: #2c1810;
}

.facture-section-title i {
    color: #b87333;
    margin-right: 8px;
}

.facture-table-wrapper {
    overflow-x: auto;
}

.facture-table {
    width: 100%;
    border-collapse: collapse;
}

.facture-table th {
    padding: 14px;
    text-align: left;
    background: #faf7f4;
    color: #6b4a3b;
    font-size: 13px;
}

.facture-table td {
    padding: 17px 14px;
    border-bottom: 1px solid #eee;
}

.article-name {
    display: flex;
    align-items: center;
    gap: 10px;
}

.article-icon {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff5eb;
    border-radius: 9px;
}

.quantity {
    background: #f3f4f6;
    padding: 5px 10px;
    border-radius: 7px;
    font-weight: 600;
}

.empty-facture {
    text-align: center;
    padding: 35px !important;
    color: #999;
}

.empty-facture i {
    display: block;
    font-size: 30px;
    margin-bottom: 10px;
}

.facture-bottom {
    margin-top: 30px;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 30px;
}

.payment-label {
    color: #777;
    margin-bottom: 8px;
}

.payment-label i {
    margin-right: 6px;
}

.payment-method {
    display: inline-block;
    padding: 10px 15px;
    border-radius: 9px;
    background: #f5f5f5;
    font-weight: 600;
}

.total-box {
    text-align: right;
}

.total-box span {
    display: block;
    font-size: 12px;
    color: #888;
    letter-spacing: 1px;
}

.total-box strong {
    display: block;
    margin-top: 5px;
    color: #b87333;
    font-size: 32px;
}

.total-box small {
    font-size: 17px;
}

.payment-success {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 30px;
    padding: 15px 18px;
    border-radius: 12px;
    background: #effaf2;
}

.payment-success > i {
    font-size: 25px;
    color: #4a7c59;
}

.payment-success strong,
.payment-success span {
    display: block;
}

.payment-success strong {
    color: #285c38;
}

.payment-success span {
    margin-top: 3px;
    font-size: 13px;
    color: #64806c;
}

.facture-footer {
    margin-top: 30px;
    padding-top: 25px;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
}

.facture-footer strong {
    color: #2c1810;
}

.facture-footer p {
    margin: 4px 0;
    color: #777;
}

.facture-footer small {
    color: #999;
}

.footer-actions {
    display: flex;
    gap: 10px;
}

.btn-secondary,
.btn-pay {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 18px;
    border-radius: 10px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    font-weight: 600;
}



@media (max-width: 768px) {

    .facture-page {
        padding: 12px;
    }

    .facture-card {
        padding: 20px;
    }

    .facture-navigation,
    .facture-header,
    .facture-bottom,
    .facture-footer {
        flex-direction: column;
        align-items: stretch;
    }

    .facture-nav-right,
    .footer-actions {
        width: 100%;
    }

    .facture-nav-btn,
    .footer-actions a,
    .footer-actions button {
        flex: 1;
        justify-content: center;
    }

    .facture-number {
        text-align: left;
    }

    .facture-info {
        grid-template-columns: 1fr 1fr;
    }

    .total-box {
        text-align: left;
    }

}

@media (max-width: 480px) {

    .facture-info {
        grid-template-columns: 1fr;
    }

    .facture-nav-right {
        flex-direction: column;
    }

}

@media print {

    body {
        background: white !important;
    }

    .facture-page {
        max-width: 100%;
        padding: 0;
    }

    .facture-navigation,
    .footer-actions {
        display: none !important;
    }

    .facture-card {
        box-shadow: none;
        border-radius: 0;
        padding: 0;
    }

}
.menu-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    text-decoration: none;
    padding: 12px 14px;
    border-radius: 10px;
    transition: all 0.2s ease;
}

.menu-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

.menu-badge {
    min-width: 24px;
    height: 24px;
    padding: 0 7px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 999px;

    background: #dc2626;
    color: white;

    font-size: 12px;
    font-weight: 700;

    box-shadow: 0 2px 6px rgba(0,0,0,.15);
}

.menu-badge.zero {
    background: #94a3b8;
}

.menu-link:hover .menu-badge {
    transform: scale(1.08);
}
.menu-badge {
    min-width: 24px;
    height: 24px;
    padding: 0 7px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border-radius: 50px;

    background: #dc2626;
    color: #fff;

    font-size: 12px;
    font-weight: 700;

    transition: .3s ease;
}

.menu-badge.zero {
    background: #94a3b8;
}

.menu-badge.updated {
    transform: scale(1.2);
}
</style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="logo">
        <div class="logo-icon"><i class="fa-solid fa-utensils"></i></div>
        <div class="logo-text"><h2>KAY-Y</h2><span>Caisse </span></div>
    </div>
    <div class="menu">
        <a href="{{ url('/caisse/dashboard') }}" class="{{ request()->is('caisse/dashboard') ? 'active' : '' }}"><i class="fa-solid fa-chart-line"></i> Tableau de bord</a>
        <a href="{{ url('/caisse/paiements') }}" class="{{ request()->is('caisse/paiements*') ? 'active' : '' }}"><i class="fa-solid fa-credit-card"></i> Paiements</a>
<a href="{{ url('/caisse/commandes') }}"
   class="{{ request()->is('caisse/commandes*') ? 'active' : '' }}">

    <i class="fa-solid fa-receipt"></i>

    <span>Commandes prêtes</span>

    <span id="commandesPretesBadge" class="menu-badge">
        {{ $countPretes ?? 0 }}
    </span>

</a>
   
     
    </div>
   <div class="sidebar-bottom">

    <a href="{{ route('logout') }}"
       class="menu logout"
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">

        <i class="fa-solid fa-right-from-bracket"></i>

        <span>Déconnexion</span>

    </a>

    <form id="logout-form"
          action="{{ route('logout') }}"
          method="POST"
          style="display:none;">

        @csrf

    </form>

</div>
</div>

<div class="main">
    <div class="topbar">
        <div class="left-top">
            <button type="button" class="fa-solid fa-bars mobile-toggle" id="toggleSidebar" aria-label="Afficher/masquer le menu" aria-expanded="false" aria-controls="sidebar"></button>
            <div>
                <h1>@yield('title','Caisse')</h1>
                <small>Gestion des paiements &amp; encaissements</small>
            </div>
        </div>
        <div class="right-top">
            <div class="search-box">
                <label for="topbarSearch" class="sr-only">Rechercher une commande</label>
                <i class="fa-solid fa-search" aria-hidden="true"></i>
                <input id="topbarSearch" type="search" name="q" placeholder="Rechercher une commande...">
            </div>
            <button type="button" class="icon-btn" aria-label="Notifications"><i class="fa-solid fa-bell" aria-hidden="true"></i><span class="badge">3</span></button>
            <button type="button" class="icon-btn" aria-label="Caisse"><i class="fa-solid fa-wallet" aria-hidden="true"></i></button>
            <div class="user">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'C',0,1)) }}</div>
                <div><h3>{{ auth()->user()->name ?? 'Caissier' }}</h3><small>Caissier</small></div>
            </div>
        </div>
    </div>

    <div class="page">
        <div class="caisse-container">
            @yield('content')
        </div>
    </div>

    <div class="footer">© {{ date('Y') }} Resto Kay-Y — Module Caisse </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const badge = document.getElementById('commandesPretesBadge');

    if (!badge) {
        return;
    }

    function updateCommandesPretes() {

        fetch("{{ route('caisse.commandes.count') }}", {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {

            if (!response.ok) {
                throw new Error('Erreur serveur');
            }

            return response.json();

        })
        .then(data => {

            const newCount = Number(data.count ?? 0);
            const oldCount = Number(badge.textContent.trim()) || 0;

            badge.textContent = newCount;

            if (newCount === 0) {
                badge.classList.add('zero');
            } else {
                badge.classList.remove('zero');
            }

            // Animation si quantité a changé
            if (newCount !== oldCount) {

                badge.classList.add('updated');

                setTimeout(() => {
                    badge.classList.remove('updated');
                }, 400);
            }

        })
        .catch(error => {
            console.error(
                'Impossible de récupérer le nombre de commandes :',
                error
            );
        });
    }

    // Vérification immédiate
    updateCommandesPretes();

    // Puis toutes les 5 secondes
    setInterval(updateCommandesPretes, 5000);

});
</script>
<script>
const sidebar=document.getElementById("sidebar");
const toggle=document.getElementById("toggleSidebar");
if(toggle){ toggle.onclick=function(){ const isOpen=sidebar.classList.toggle("show"); toggle.setAttribute("aria-expanded",isOpen); }; }

function updateClock(){
    let now=new Date();
    let options={ weekday:'long', day:'2-digit', month:'long', year:'numeric' };
    let date=now.toLocaleDateString('fr-FR',options);
    let heure=now.toLocaleTimeString('fr-FR');
    let el=document.getElementById("liveClock");
    if(el){ el.innerHTML=date+"<br><b>"+heure+"</b>"; }
}
updateClock(); setInterval(updateClock,1000);

function showNotification(title,message,color="#B87333"){
    const notif=document.createElement("div");
    notif.style.cssText="position:fixed;top:30px;right:30px;width:340px;background:var(--card-bg);border-left:5px solid "+color+";padding:18px;border-radius:18px;box-shadow:0 15px 35px rgba(44,24,16,.15);z-index:99999;opacity:0;transform:translateX(120px);transition:.45s;font-family:var(--font-body);color:var(--ink);border:1px solid var(--linen);border-left-width:5px;";
    notif.innerHTML="<h3 style='margin-bottom:6px;font-family:var(--font-bistro);font-size:18px;'>"+title+"</h3><div style='font-size:14px;'>"+message+"</div>";
    document.body.appendChild(notif);
    setTimeout(()=>{ notif.style.opacity="1"; notif.style.transform="translateX(0)"; },100);
    setTimeout(()=>{ notif.style.opacity="0"; notif.style.transform="translateX(120px)"; setTimeout(()=>notif.remove(),500); },4500);
}

const darkBtn=document.getElementById("darkMode");
const DARK_CLASS="dark-mode"; const DARK_STORAGE_KEY="caisse-dark";
function setDarkMode(enabled){ document.body.classList.toggle(DARK_CLASS,enabled); localStorage.setItem(DARK_STORAGE_KEY,enabled); if(darkBtn) darkBtn.setAttribute("aria-pressed",enabled); }
if(darkBtn){ darkBtn.onclick=function(){ setDarkMode(!document.body.classList.contains(DARK_CLASS)); }; }
if(localStorage.getItem(DARK_STORAGE_KEY)=="true") setDarkMode(true);

const scrollBtn=document.getElementById("scrollTop");
if(scrollBtn){
    window.addEventListener("scroll",()=>{ scrollBtn.style.display=window.scrollY>300?"block":"none"; });
    scrollBtn.onclick=()=>window.scrollTo({top:0,behavior:"smooth"});
}

document.querySelectorAll("[data-tooltip]").forEach(el=>{
    el.addEventListener("mouseenter",()=>{
        const tip=document.createElement("div");
        tip.style.cssText="position:fixed;background:var(--espresso);color:#fff;padding:8px 14px;border-radius:10px;font-size:12px;z-index:9999;pointer-events:none;font-family:var(--font-body);";
        tip.innerHTML=el.dataset.tooltip; document.body.appendChild(tip);
        const r=el.getBoundingClientRect();
        tip.style.left=(r.left+window.scrollX)+"px"; tip.style.top=(r.top+window.scrollY-40)+"px"; el._tooltip=tip;
    });
    el.addEventListener("mouseleave",()=>{ if(el._tooltip){ el._tooltip.remove(); } });
});

document.addEventListener("DOMContentLoaded",()=>{ updateClock(); showNotification("Bienvenue","Caisse Bistro prête.","#4A7C59"); });
</script>

<audio id="notifSound" preload="auto"><source src="{{ asset('sounds/notification.mp3') }}" type="audio/mpeg"></audio>

<div id="liveClock" style="position:fixed;bottom:25px;right:25px;background:var(--card-bg);padding:14px 18px;border-radius:16px;box-shadow:0 10px 30px rgba(44,24,16,.12);text-align:center;font-size:13px;color:var(--ink);z-index:999;min-width:180px;font-family:var(--font-mono);border:1px solid var(--linen);"></div>


<button id="scrollTop" type="button" aria-label="Retourner en haut" style="position:fixed;bottom:185px;right:25px;width:50px;height:50px;border:none;border-radius:50%;cursor:pointer;background:var(--brass);color:#fff;font-size:18px;display:none;box-shadow:0 10px 25px rgba(184,115,51,.35);z-index:999;transition:.35s;"><i class="fa-solid fa-arrow-up" aria-hidden="true"></i></button>

@stack('scripts')

</body>
</html>