<?php
/**
 * Global Header Component
 * Cyber-Tech & Futuristic GPU Store
 */
require_once __DIR__ . '/../config/db.php';

$pageTitle = isset($pageTitle) ? $pageTitle . " | " . SITE_NAME : SITE_NAME . " - " . SITE_TAGLINE;
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="th" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="CYBER-GPU STORE ศูนย์รวมการ์ดจอเกมมิ่งระดับไฮเอนด์และฮาร์ดแวร์ AI Compute NVIDIA, AMD, Intel สต็อกแท้ ประกันศูนย์">
  <meta name="keywords" content="GPU, การ์ดจอ, RTX 4090, RTX 4080, RX 7900 XTX, Intel Arc, Gaming Hardware, Computer, PC">
  <title><?= e($pageTitle) ?></title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            cyberDark: '#060911',
            cyberCard: '#0f172a',
            cyberNvidia: '#00FF66',
            cyberAmd: '#FF0055',
            cyberIntel: '#00F0FF',
            cyberPurple: '#8B5CF6'
          },
          fontFamily: {
            scifi: ['Orbitron', 'Rajdhani', 'sans-serif'],
            thai: ['Prompt', 'Kanit', 'sans-serif']
          }
        }
      }
    }
  </script>

  <!-- Google Fonts: Orbitron, Rajdhani, Prompt -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;600;700;800;900&family=Prompt:wght@300;400;500;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">

  <!-- Font Awesome 6 Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>

  <!-- Chart.js CDN for Reports -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  <!-- Custom Cyber Stylesheet -->
  <link rel="stylesheet" href="assets/css/cyber-style.css">
</head>
<body class="flex flex-col min-h-screen text-slate-200 antialiased selection:bg-cyan-500 selection:text-black">
