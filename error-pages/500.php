<?php
$title = '500 - Internal Server Error';
$bodyClass = 'error-page';

http_response_code(500);

// Simple error page without dependencies to avoid further errors
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Survey Platform</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
        }
        
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }
        
        .error-content {
            text-align: center;
            max-width: 600px;
            padding: 2rem;
        }
        
        .error-code {
            font-size: 6rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            opacity: 0.8;
        }
        
        .error-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .error-description {
            font-size: 1.125rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .btn-primary:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .btn-outline {
            background: transparent;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        
        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        @media (max-width: 768px) {
            .error-code {
                font-size: 4rem;
            }
            
            .error-title {
                font-size: 1.5rem;
            }
            
            .error-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body class="<?php echo $bodyClass; ?>">
    <div class="error-container">
        <div class="error-content">
            <div class="error-code">500</div>
            <h1 class="error-title">Internal Server Error</h1>
            <p class="error-description">
                Sorry, something went wrong on our end. Our team has been notified and is working on fixing the issue.
            </p>
            <div class="error-actions">
                <a href="javascript:location.reload()" class="btn btn-primary">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Try Again
                </a>
                <a href="/" class="btn btn-outline">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m0 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10M9 21h6"/>
                    </svg>
                    Go Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>
