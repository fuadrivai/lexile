<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MHIS Assessment</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --mhis-primary: #4361ee;
            --mhis-secondary: #3f37c9;
            --mhis-light: #f8f9fa;
            --mhis-dark: #212529;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .mhis-container {
            max-width: 600px;
            margin-top: 5rem;
            animation: fadeIn 0.8s ease-out;
        }

        .mhis-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .mhis-header {
            background: var(--mhis-primary);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .mhis-header::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
            transform: rotate(30deg);
        }

        .mhis-body {
            padding: 2.5rem;
            background-color: white;
        }

        .mhis-title {
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .mhis-subtitle {
            font-weight: 300;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--mhis-primary);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }

        .input-group-text {
            background-color: #f1f3f5;
            border: 1px solid #e0e0e0;
        }

        .btn-mhis {
            background-color: var(--mhis-primary);
            border: none;
            padding: 12px 24px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            transition: all 0.3s;
        }

        .btn-mhis:hover {
            background-color: var(--mhis-secondary);
            transform: translateY(-2px);
        }

        .required-field::after {
            content: " *";
            color: #dc3545;
        }

        .welcome-message {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid var(--mhis-primary);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 576px) {
            .mhis-container {
                margin-top: 2rem;
                padding: 0 15px;
            }

            .mhis-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container mhis-container">
        <div class="card mhis-card">
            <div class="mhis-header">
                <h1 class="mhis-title">MHIS Assessment</h1>
                <p class="mhis-subtitle">Please complete the form to begin</p>
            </div>

            <div class="mhis-body">
                <div class="welcome-message">
                    <i class="fas fa-hand-wave me-2"></i> Welcome to MHIS Assessment! We're glad you're here.
                </div>

                <form id="mhisForm" novalidate method="POST" action="/lexile-2/">
                    @csrf
                    <div class="mb-4">
                        <label for="nameInput" class="form-label fw-bold required-field">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="name" class="form-control" id="nameInput"
                                placeholder="Enter your full name" required>
                        </div>
                        <div class="invalid-feedback">
                            Please provide your full name.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="emailInput" class="form-label fw-bold required-field">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" class="form-control" id="emailInput"
                                placeholder="Enter your email" required>
                        </div>
                        <div class="invalid-feedback">
                            Please provide a valid email address.
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="gradeInput" class="form-label fw-bold required-field">Grade</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                            <select name="grade" id="gradeSelect" class="form-select form-control" required>
                                <option value="" disabled selected>Select your grade</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">Grade {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="invalid-feedback">
                            Please provide a valid grade.
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-mhis">
                            <i class="fas fa-paper-plane me-2"></i> Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Form validation
        (function() {
            'use strict';
            // Fetch the form we want to apply validation to
            const form = document.getElementById('mhisForm');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                form.classList.add('was-validated');
            }, false);

            // Custom validation for the email field
            const emailInput = document.getElementById('emailInput');
            emailInput.addEventListener('input', function() {
                if (emailInput.validity.typeMismatch) {
                    emailInput.setCustomValidity('Please enter a valid email address');
                } else {
                    emailInput.setCustomValidity('');
                }
            });
        })();

        // Animation for welcome message
        document.addEventListener('DOMContentLoaded', function() {
            const welcomeMsg = document.querySelector('.welcome-message');
            setTimeout(() => {
                welcomeMsg.style.opacity = '1';
                welcomeMsg.style.transform = 'translateY(0)';
            }, 300);

            welcomeMsg.style.opacity = '0';
            welcomeMsg.style.transform = 'translateY(-10px)';
            welcomeMsg.style.transition = 'all 0.5s ease-out';
        });
    </script>
</body>

</html>
