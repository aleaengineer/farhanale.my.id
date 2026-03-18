<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle . ' - Admin | MikroTik Blog') : 'Admin | MikroTik Blog'; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?php echo SITE_URL; ?>/assets/css/admin.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #8B5CF6;
            --secondary: #EC4899;
            --mikrotik: #2563EB;
            --dark: #1F2937;
            --light: #F9FAFB;
            --gray: #6B7280;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
        }
    </style>
</head>
<body>