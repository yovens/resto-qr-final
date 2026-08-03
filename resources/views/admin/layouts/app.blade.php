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
</style>


</head>


<body>



<!-- SIDEBAR -->

<div class="sidebar" id="sidebar">


<div class="logo">

<i class="fa-solid fa-utensils"></i>

RESTO KAY-Y

</div>



<div class="menu-title">

Principal

</div>


<a href="/admin/dashboard">

<i class="fa-solid fa-chart-line"></i>

Dashboard

</a>



<div class="menu-title">

Restaurant

</div>


<a href="/admin/plats">

<i class="fa-solid fa-burger"></i>

Menu

</a>


<a href="/admin/categories">

<i class="fa-solid fa-folder"></i>

Catégories

</a>



<a href="/admin/tables">

<i class="fa-solid fa-chair"></i>

Tables

</a>




<a href="/cuisine">


<i class="fa-solid fa-fire"></i>


Cuisine


<span class="badge">

5

</span>


</a>





<div class="menu-title">

Finance

</div>


<a href="/admin/ventes">


<i class="fa-solid fa-money-bill"></i>


Ventes


</a>



</div>





<!-- MAIN -->

<div class="main">



<div class="topbar">


<i class="fa-solid fa-bars menu-toggle"
onclick="toggleMenu()">
</i>



<div class="search-box">


<i class="fa fa-search"></i>


<input placeholder="Rechercher...">


</div>



<div class="top-actions">



<div class="notification">


<i class="fa-solid fa-bell"></i>

<span>
3
</span>

</div>




<div class="profile">


<div class="avatar">

A

</div>


<div>

<strong>
Admin
</strong>

<br>

<small>
Restaurant
</small>

</div>


</div>



</div>


</div>





<div class="content">


@yield('content')


</div>


</div>





<script>


function toggleMenu(){


document
.getElementById('sidebar')
.classList.toggle('show');


}


</script>



</body>

</html>