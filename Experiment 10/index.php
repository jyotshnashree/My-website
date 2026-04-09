<?php
// Create data directory if it doesn't exist
$dataDir = __DIR__ . '/data';
$uploadsDir = __DIR__ . '/uploads';

if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0755, true);
}

// Get all saved forms
$savedForms = [];
if (is_dir($dataDir)) {
    $files = glob($dataDir . '/*.json');
    foreach ($files as $file) {
        $savedForms[] = json_decode(file_get_contents($file), true);
    }
    // Sort by timestamp in descending order
    usort($savedForms, function($a, $b) {
        return $b['timestamp'] - $a['timestamp'];
    });
}

$showFilled = isset($_GET['show']) && $_GET['show'] === 'filled';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Form Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', 'Inter', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0F1419 0%, #1a2332 50%, #16213e 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: rgba(16, 185, 129, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
            z-index: -1;
        }

        body::after {
            content: '';
            position: fixed;
            bottom: -20%;
            left: -5%;
            width: 400px;
            height: 400px;
            background: rgba(217, 119, 180, 0.08);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite reverse;
            z-index: -1;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(30px) translateX(20px); }
        }

        .container {
            background: rgba(20, 25, 35, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 40px;
            box-shadow: 0 20px 60px rgba(16, 185, 129, 0.15), 0 0 80px rgba(217, 119, 180, 0.08), 0 5px 20px rgba(255, 193, 7, 0.06);
            padding: 50px;
            width: 100%;
            max-width: 900px;
            border: 3px solid rgba(16, 185, 129, 0.25);
            animation: slideUp 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(2deg); }
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(125, 217, 179, 0.7); }
            50% { box-shadow: 0 0 0 15px rgba(125, 217, 179, 0); }
        }

        @keyframes wiggle {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(1deg); }
            75% { transform: rotate(-1deg); }
        }

        .header {
            text-align: center;
            margin-bottom: 50px;
            animation: fadeIn 0.9s ease-out;
        }

        .header-icon {
            font-size: 70px;
            background: linear-gradient(135deg, #10B981 0%, #D977B4 50%, #FFC107 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            animation: bounce 2s ease-in-out infinite;
            display: inline-block;
        }

        h1 {
            color: #FFFFFF;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .subtitle {
            color: #A0A6B0;
            font-size: 14px;
            font-weight: 500;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .form-group {
            margin-bottom: 25px;
            animation: slideUp 0.6s ease-out forwards;
            opacity: 0;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.15s; }
        .form-group:nth-child(3) { animation-delay: 0.2s; }
        .form-group:nth-child(4) { animation-delay: 0.25s; }
        .form-group:nth-child(5) { animation-delay: 0.3s; }
        .form-group:nth-child(6) { animation-delay: 0.35s; }
        .form-group:nth-child(7) { animation-delay: 0.4s; }
        .form-group:nth-child(8) { animation-delay: 0.45s; }
        .form-group:nth-child(9) { animation-delay: 0.5s; }

        /* Form Section Styles */
        .form-section {
            margin-bottom: 45px;
            padding: 30px;
            background: rgba(30, 40, 50, 0.5);
            border-radius: 25px;
            border: 2px solid rgba(16, 185, 129, 0.15);
            animation: slideUp 0.6s ease-out forwards;
            opacity: 0;
        }

        .form-section:nth-of-type(1) { animation-delay: 0.1s; }
        .form-section:nth-of-type(2) { animation-delay: 0.2s; }
        .form-section:nth-of-type(3) { animation-delay: 0.3s; }
        .form-section:nth-of-type(4) { animation-delay: 0.4s; }

        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(16, 185, 129, 0.3);
            cursor: default;
        }

        .section-icon {
            font-size: 28px;
            background: linear-gradient(135deg, #10B981 0%, #D977B4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-right: 15px;
        }

        .section-title {
            color: #FFFFFF;
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-full {
            grid-column: 1 / -1;
        }

        @media (max-width: 768px) {
            .form-grid-2 {
                grid-template-columns: 1fr;
            }

            .form-section {
                padding: 20px;
            }

            .section-header {
                margin-bottom: 20px;
            }
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #FFFFFF;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
        }

        input[type="text"],
        input[type="email"],
        input[type="date"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 14px 16px;
            border: 3px solid #2D3F52;
            border-radius: 20px;
            font-family: inherit;
            font-size: 14px;
            background: #FAFBFC;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        input[type="text"]::placeholder,
        input[type="email"]::placeholder,
        textarea::placeholder {
            color: #5A6270;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="date"]:focus,
        textarea:focus {
            outline: none;
            border-color: #10B981;
            background: #1a2332;
            color: #FFFFFF;
            box-shadow: 0 0 0 8px rgba(16, 185, 129, 0.12), 0 0 20px rgba(16, 185, 129, 0.25);
            transform: scale(1.02);
        }

        input[type="file"] {
            padding: 10px;
            cursor: pointer;
        }

        input[type="file"]::-webkit-file-upload-button {
            padding: 10px 18px;
            border: none;
            border-radius: 15px;
            background: linear-gradient(135deg, #10B981 0%, #D977B4 100%);
            color: white;
            cursor: pointer;
            font-weight: 700;
            font-size: 13px;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            margin-right: 10px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
        }

        input[type="file"]::-webkit-file-upload-button:hover {
            transform: translateY(-4px) scale(1.08);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.35);
        }

        textarea {
            resize: vertical;
            min-height: 120px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #FFFFFF;
        }

        textarea::placeholder {
            color: #5A6270;
        }

        .radio-group,
        .checkbox-group {
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        .radio-item,
        .checkbox-item {
            display: flex;
            align-items: center;
            position: relative;
            animation: fadeIn 0.8s ease-out forwards;
            opacity: 0;
        }

        .radio-item:nth-child(1) { animation-delay: 0.1s; }
        .radio-item:nth-child(2) { animation-delay: 0.2s; }
        .radio-item:nth-child(3) { animation-delay: 0.3s; }
        .checkbox-item:nth-child(1) { animation-delay: 0.1s; }
        .checkbox-item:nth-child(2) { animation-delay: 0.2s; }
        .checkbox-item:nth-child(3) { animation-delay: 0.3s; }
        .checkbox-item:nth-child(4) { animation-delay: 0.4s; }
        .checkbox-item:nth-child(5) { animation-delay: 0.5s; }
        .checkbox-item:nth-child(6) { animation-delay: 0.6s; }

        input[type="radio"],
        input[type="checkbox"] {
            width: 24px;
            height: 24px;
            margin-right: 10px;
            cursor: pointer;
            accent-color: #10B981;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        input[type="radio"]:hover,
        input[type="checkbox"]:hover {
            transform: scale(1.25) rotate(10deg);
        }

        .radio-item label,
        .checkbox-item label {
            margin: 0;
            font-weight: 500;
            color: #FFFFFF;
            cursor: pointer;
            font-size: 14px;
        }

        .file-preview {
            margin-top: 10px;
            padding: 10px;
            background: #0F1419;
            border-radius: 8px;
            font-size: 12px;
            color: #A0A6B0;
        }

        .button-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 50px;
        }

        button {
            padding: 18px 32px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        button:active::before {
            width: 300px;
            height: 300px;
        }

        .btn-save {
            background: linear-gradient(135deg, #10B981 0%, #D977B4 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
            position: relative;
        }

        .btn-save::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 25px;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.2) 50%, transparent 100%);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .btn-save:hover {
            transform: translateY(-6px) scale(1.05) rotate(-1deg);
            box-shadow: 0 15px 40px rgba(16, 185, 129, 0.45), 0 0 30px rgba(217, 119, 180, 0.3);
        }

        .btn-save:active {
            transform: translateY(-2px) scale(0.98);
        }

        .btn-display {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: 3px solid rgba(102, 126, 234, 0.5);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
            position: relative;
            overflow: visible;
        }

        .btn-display .profile-badge {
            position: absolute;
            top: -12px;
            right: -12px;
            background: linear-gradient(135deg, #FFC107 0%, #FF6B6B 100%);
            color: white;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
            border: 2px solid rgba(255, 255, 255, 0.3);
            animation: badgePulse 2s ease-in-out infinite;
        }

        @keyframes badgePulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.15); }
        }

        .btn-display:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            transform: translateY(-6px) scale(1.05) rotate(1deg);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4), 0 0 30px rgba(255, 107, 107, 0.2);
            border-color: #D977B4;
        }

        .btn-display:active {
            transform: translateY(-2px) scale(0.98);
        }

        .btn-back {
            background: linear-gradient(135deg, #FF6B6B 0%, #D977B4 100%);
            color: white;
            width: 100%;
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
        }

        .btn-back::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 25px;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.15) 50%, transparent 100%);
            animation: shine 3s infinite;
        }

        .btn-back:hover {
            transform: translateY(-6px) scale(1.02) rotate(-1deg);
            box-shadow: 0 15px 40px rgba(255, 107, 107, 0.45), 0 0 25px rgba(217, 119, 180, 0.3);
        }

        .btn-back:active {
            transform: translateY(-2px) scale(0.98);
        }

        /* Filled Forms Styles */
        .filled-forms-container {
            display: none;
        }

        .filled-forms-container.show {
            display: block;
        }

        .form-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .form-card {
            border: none;
            border-radius: 30px;
            padding: 30px;
            background: linear-gradient(135deg, #1a2332 0%, #16213e 100%);
            cursor: pointer;
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.08);
            position: relative;
            overflow: hidden;
        }

        .form-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #10B981 0%, #D977B4 50%, #FFC107 100%);
            transition: left 0.5s ease;
        }

        .form-card:hover::before {
            left: 100%;
        }

        .form-card:hover {
            box-shadow: 0 20px 50px rgba(16, 185, 129, 0.2);
            transform: translateY(-12px) scale(1.04) rotate(-2deg);
        }

        .card-photo {
            width: 90px;
            height: 90px;
            border-radius: 25px;
            margin-bottom: 20px;
            object-fit: cover;
            border: 5px solid #7DD9D9;
            box-shadow: 0 5px 20px rgba(16, 185, 129, 0.28);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .form-card:hover .card-photo {
            transform: scale(1.1) rotate(5deg);
            border-color: #D977B4;
        }

        .form-card h3 {
            color: #FFFFFF;
            margin-bottom: 12px;
            font-size: 18px;
            font-weight: 700;
        }

        .form-card p {
            color: #A0A6B0;
            font-size: 13px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .form-card p i {
            margin-right: 8px;
            color: #10B981;
            width: 16px;
        }

        .form-details {
            background: linear-gradient(135deg, #1a2332 0%, #16213e 100%);
            border-radius: 35px;
            padding: 45px;
            margin-bottom: 40px;
            border: 3px solid rgba(16, 185, 129, 0.2);
            box-shadow: 0 15px 50px rgba(16, 185, 129, 0.1);
            animation: slideUp 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .form-details-header {
            display: flex;
            align-items: center;
            margin-bottom: 35px;
            padding-bottom: 25px;
            border-bottom: 3px dashed #2D3F52;
        }

        .detail-photo {
            width: 140px;
            height: 140px;
            border-radius: 30px;
            object-fit: cover;
            margin-right: 30px;
            border: 5px solid #7DD9D9;
            box-shadow: 0 10px 35px rgba(184, 168, 232, 0.3);
        }

        .detail-header-text h2 {
            color: #FFFFFF;
            margin-bottom: 8px;
            font-size: 26px;
            font-weight: 700;
        }

        .detail-header-text p {
            color: #A0A6B0;
            font-size: 14px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .detail-item {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .detail-label {
            color: #FF9DB9;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .detail-label i {
            margin-right: 8px;
            font-size: 14px;
        }

        .detail-value {
            color: #1a1a2e;
            font-size: 15px;
            font-weight: 500;
            word-break: break-word;
        }

        .detail-item-full {
            grid-column: 1 / -1;
        }

        .resume-link {
            display: inline-block;
            color: #10B981;
            text-decoration: none;
            font-weight: 700;
            padding: 12px 18px;
            background: rgba(16, 185, 129, 0.15);
            border-radius: 15px;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 2px solid rgba(16, 185, 129, 0.28);
        }

        .resume-link:hover {
            background: rgba(217, 119, 180, 0.2);
            color: #D977B4;
            transform: scale(1.1) rotate(-3deg);
            border-color: #D977B4;
        }

        .empty-message {
            text-align: center;
            padding: 60px 40px;
            color: #5A6270;
            font-size: 18px;
            animation: fadeIn 0.6s ease-out;
        }

        .empty-icon {
            font-size: 60px;
            color: #2D3F52;
            margin-bottom: 20px;
        }

        .success-message {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: white;
            padding: 20px;
            border-radius: 25px;
            margin-bottom: 35px;
            text-align: center;
            font-weight: 700;
            font-size: 15px;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
            animation: slideDown 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 3px solid rgba(255, 255, 255, 0.2);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 25px;
            }

            .details-grid {
                grid-template-columns: 1fr;
            }

            .button-group {
                grid-template-columns: 1fr;
            }

            .form-list {
                grid-template-columns: 1fr;
            }

            .form-details-header {
                flex-direction: column;
                text-align: center;
            }

            .detail-photo {
                margin-right: 0;
                margin-bottom: 20px;
            }

            h1 {
                font-size: 24px;
            }

            .header-icon {
                font-size: 40px;
            }

            .form-grid-2 {
                grid-template-columns: 1fr;
            }

            .form-section {
                padding: 20px;
                margin-bottom: 30px;
            }

            .section-header {
                margin-bottom: 20px;
            }

            .section-icon {
                font-size: 22px;
            }

            .section-title {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Form Input Section -->
        <div id="formSection" style="<?php echo $showFilled ? 'display: none;' : ''; ?>">
            <div class="header">
                <div class="header-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h1>Professional Profile Form</h1>
                <p class="subtitle">Complete your profile with all necessary information</p>
            </div>

            <?php if (isset($_GET['success']) && $_GET['success'] === '1'): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> Form saved successfully!
                </div>
            <?php endif; ?>

            <form method="POST" action="save_form.php" enctype="multipart/form-data">
                <!-- Personal Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-user section-icon"></i>
                        <h3 class="section-title">Personal Information</h3>
                    </div>
                    
                    <div class="form-grid-2">
                        <!-- Full Name -->
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Full Name *</label>
                            <div class="input-wrapper">
                                <input type="text" name="username" placeholder="John Doe" required>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email Address *</label>
                            <div class="input-wrapper">
                                <input type="email" name="email" placeholder="john@example.com" required>
                            </div>
                        </div>

                        <!-- Date of Birth -->
                        <div class="form-group">
                            <label><i class="fas fa-birthday-cake"></i> Date of Birth *</label>
                            <div class="input-wrapper">
                                <input type="date" name="dob" required>
                            </div>
                        </div>

                        <!-- Gender -->
                        <div class="form-group">
                            <label><i class="fas fa-venus-mars"></i> Gender *</label>
                            <div class="radio-group">
                                <div class="radio-item">
                                    <input type="radio" id="male" name="gender" value="Male" required>
                                    <label for="male">Male</label>
                                </div>
                                <div class="radio-item">
                                    <input type="radio" id="female" name="gender" value="Female">
                                    <label for="female">Female</label>
                                </div>
                                <div class="radio-item">
                                    <input type="radio" id="other" name="gender" value="Other">
                                    <label for="other">Other</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic/Professional Section -->
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-graduation-cap section-icon"></i>
                        <h3 class="section-title">Academic & Professional</h3>
                    </div>
                    
                    <div class="form-grid-2">
                        <!-- USN -->
                        <div class="form-group">
                            <label><i class="fas fa-id-card"></i> USN/ID Number *</label>
                            <div class="input-wrapper">
                                <input type="text" name="usn" placeholder="24BTRXXXXX" required>
                            </div>
                        </div>

                        <!-- Technical Skills -->
                        <div class="form-group">
                            <label><i class="fas fa-code"></i> Technical Skills *</label>
                            <div class="checkbox-group">
                                <div class="checkbox-item">
                                    <input type="checkbox" id="html" name="languages[]" value="HTML">
                                    <label for="html">HTML</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="css" name="languages[]" value="CSS">
                                    <label for="css">CSS</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="js" name="languages[]" value="JavaScript">
                                    <label for="js">JS</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="java" name="languages[]" value="Java">
                                    <label for="java">Java</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="python" name="languages[]" value="Python">
                                    <label for="python">Python</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="rust" name="languages[]" value="Rust">
                                    <label for="rust">Rust</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents Section -->
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-file-upload section-icon"></i>
                        <h3 class="section-title">Documents & Media</h3>
                    </div>
                    
                    <div class="form-grid-2">
                        <!-- Photo Upload -->
                        <div class="form-group">
                            <label><i class="fas fa-camera"></i> Profile Photo *</label>
                            <div class="input-wrapper">
                                <input type="file" name="photo" accept="image/*" required id="photoInput">
                                <div class="file-preview" id="photoPreview"></div>
                            </div>
                        </div>

                        <!-- Resume Upload -->
                        <div class="form-group">
                            <label><i class="fas fa-file-pdf"></i> Resume/CV *</label>
                            <div class="input-wrapper">
                                <input type="file" name="resume" accept=".pdf,.doc,.docx" required id="resumeInput">
                                <div class="file-preview" id="resumePreview"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-pen-fancy section-icon"></i>
                        <h3 class="section-title">About You</h3>
                    </div>
                    
                    <!-- Description -->
                    <div class="form-group">
                        <label><i class="fas fa-pen-fancy"></i> About Yourself *</label>
                        <textarea name="description" placeholder="Tell us about yourself, your experience, and career goals..." required></textarea>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="button-group">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-check-circle"></i> <span>Submit</span>
                    </button>
                    <button type="button" class="btn-display" onclick="displayFilledForms()">
                        <i class="fas fa-eye"></i> <span>View the Submitted</span>
                        <span class="profile-badge" id="profileCount">0</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Filled Forms Display Section -->
        <div id="filledFormsSection" class="filled-forms-container" style="<?php echo $showFilled ? 'display: block;' : 'display: none;'; ?>">
            <div class="header">
                <div class="header-icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h1>Saved Profiles</h1>
                <p class="subtitle">View and manage all your saved profiles</p>
            </div>

            <?php if (empty($savedForms)): ?>
                <div class="empty-message">
                    <div class="empty-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <p>No profiles saved yet. Create one to get started!</p>
                </div>
            <?php else: ?>
                <div class="form-list">
                    <?php foreach ($savedForms as $index => $form): ?>
                        <div class="form-card" onclick="viewForm(<?php echo $index; ?>)">
                            <?php if (!empty($form['photo_path']) && file_exists($form['photo_path'])): ?>
                                <img src="<?php echo htmlspecialchars($form['photo_path']); ?>" alt="Profile" class="card-photo">
                            <?php endif; ?>
                            <h3>
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($form['username']); ?>
                            </h3>
                            <p>
                                <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($form['email']); ?>
                            </p>
                            <p>
                                <i class="fas fa-id-card"></i> <?php echo htmlspecialchars($form['usn']); ?>
                            </p>
                            <p>
                                <i class="fas fa-calendar"></i> <?php echo date('M d, Y h:i A', $form['timestamp']); ?>
                            </p>
                            <p style="color: #667eea; margin-top: 15px; cursor: pointer; font-weight: 600;">
                                <i class="fas fa-arrow-right"></i> View Full Profile
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <button type="button" class="btn-back" onclick="backToForm()">
                <i class="fas fa-plus-circle"></i> Add More Profiles
            </button>
        </div>
    </div>

    <script>
        // Update profile badge count
        function updateProfileCount() {
            const forms = <?php echo json_encode($savedForms); ?>;
            const badge = document.getElementById('profileCount');
            if (badge) {
                badge.textContent = forms.length;
                if (forms.length > 0) {
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        }
        
        // Initialize profile count on page load
        window.addEventListener('DOMContentLoaded', updateProfileCount);

        // File preview functionality
        document.getElementById('photoInput')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('photoPreview');
            if (file) {
                preview.textContent = '✓ ' + file.name + ' (' + (file.size / 1024).toFixed(2) + ' KB)';
            }
        });

        document.getElementById('resumeInput')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('resumePreview');
            if (file) {
                preview.textContent = '✓ ' + file.name + ' (' + (file.size / 1024).toFixed(2) + ' KB)';
            }
        });

        function displayFilledForms() {
            document.getElementById('formSection').style.display = 'none';
            document.getElementById('filledFormsSection').style.display = 'block';
            window.history.pushState({}, '', '?show=filled');
            window.scrollTo(0, 0);
        }

        function backToForm() {
            document.getElementById('formSection').style.display = 'block';
            document.getElementById('filledFormsSection').style.display = 'none';
            window.history.pushState({}, '', '?');
            const successMsg = document.querySelector('.success-message');
            if (successMsg) {
                successMsg.style.display = 'none';
            }
            window.scrollTo(0, 0);
        }

        function viewForm(index) {
            const forms = <?php echo json_encode($savedForms); ?>;
            if (forms[index]) {
                const form = forms[index];
                const photoHtml = form.photo_path ? `<img src="${form.photo_path}" alt="Profile" class="detail-photo">` : '';
                
                const details = `
                    <div class="form-details">
                        <div class="form-details-header">
                            ${photoHtml}
                            <div class="detail-header-text">
                                <h2>${form.username}</h2>
                                <p>
                                    <i class="fas fa-calendar-alt"></i>
                                    Saved on ${new Date(form.timestamp * 1000).toLocaleString()}
                                </p>
                            </div>
                        </div>
                        
                        <div class="details-grid">
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-envelope"></i> Email</div>
                                <div class="detail-value">${form.email}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-id-card"></i> USN</div>
                                <div class="detail-value">${form.usn}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-venus-mars"></i> Gender</div>
                                <div class="detail-value">${form.gender}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-birthday-cake"></i> Date of Birth</div>
                                <div class="detail-value">${form.dob}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-code"></i> Technical Skills</div>
                                <div class="detail-value">${form.languages.join(', ')}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-file"></i> Resume</div>
                                <div class="detail-value">
                                    ${form.resume_path ? '<a href="' + form.resume_path + '" class="resume-link" download><i class="fas fa-download"></i> Download Resume</a>' : 'N/A'}
                                </div>
                            </div>
                            <div class="detail-item detail-item-full">
                                <div class="detail-label"><i class="fas fa-pen-fancy"></i> About</div>
                                <div class="detail-value">${form.description}</div>
                            </div>
                        </div>
                    </div>
                `;
                const formList = document.querySelector('.form-list');
                formList.insertAdjacentHTML('beforebegin', details);
                formList.style.display = 'none';
            }
        }
    </script>
</body>
</html>
