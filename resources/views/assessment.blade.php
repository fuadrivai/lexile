<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Your Assessment</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --dark-color: #212529;
            --light-color: #f8f9fa;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e2e8f0 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header-section {
            text-align: center;
            margin-bottom: 3rem;
            animation: fadeInDown 0.8s ease-out;
        }

        .header-title {
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
        }

        .header-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        .header-subtitle {
            color: #6c757d;
            font-weight: 300;
            max-width: 600px;
            margin: 0 auto;
        }

        .assessment-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }

        .assessment-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
            height: 100%;
            background-color: white;
            position: relative;
            overflow: hidden;
        }

        .assessment-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: var(--primary-color);
        }

        .assessment-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
        }

        .card-reading::before {
            background: linear-gradient(90deg, #4361ee, #4cc9f0);
        }

        .card-math::before {
            background: linear-gradient(90deg, #7209b7, #f72585);
        }

        .card-icon {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .card-reading .card-icon {
            background: linear-gradient(135deg, #4361ee, #4cc9f0);
            -webkit-background-clip: text;
            background-clip: text;
        }

        .card-math .card-icon {
            background: linear-gradient(135deg, #7209b7, #f72585);
            -webkit-background-clip: text;
            background-clip: text;
        }

        .card-title {
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 1rem;
        }

        .card-text {
            color: #6c757d;
            margin-bottom: 2rem;
        }

        .btn-assessment {
            border: none;
            padding: 10px 25px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .btn-reading {
            background: linear-gradient(90deg, #4361ee, #4cc9f0);
            color: white;
        }

        .btn-math {
            background: linear-gradient(90deg, #7209b7, #f72585);
            color: white;
        }

        .btn-assessment:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            color: white;
        }

        .btn-assessment:active {
            transform: translateY(0);
        }

        .assessment-details {
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .detail-item {
            margin-bottom: 0.5rem;
        }

        .detail-item i {
            margin-right: 8px;
            color: var(--primary-color);
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .assessment-card {
            animation: fadeIn 0.6s ease-out forwards;
        }

        .card-reading {
            animation-delay: 0.2s;
        }

        .card-math {
            animation-delay: 0.4s;
        }

        @media (max-width: 768px) {
            .assessment-container {
                padding: 1rem;
            }

            .header-title {
                font-size: 1.8rem;
            }

            .header-subtitle {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="header-section">
            <h1 class="header-title">Select Your Assessment</h1>
            <p class="header-subtitle">Choose the test you want to take from our available assessments below</p>
        </div>

        <div class="assessment-container">
            <div class="row g-4">
                <!-- Reading Assessment Card -->
                <div class="col-md-6">
                    <div class="assessment-card card-reading p-4">
                        <div class="text-center">
                            <i class="fas fa-book-open card-icon"></i>
                            <h3 class="card-title">Reading Lexile Assessment</h3>
                            <p class="card-text">Measure your reading comprehension skills and vocabulary level with our
                                standardized reading test.</p>
                        </div>

                        <div class="assessment-details">
                            <div class="detail-item">
                                <i class="fas fa-graduation-cap"></i> Grade: {{ $grade }}
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-clock"></i> Duration: {{ $lexile->duration }} minutes
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-question-circle"></i> {{ $lexile->total_question }} multiple-choice
                                questions
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button class="btn btn-reading btn-assessment"
                                onclick="window.location.href='/reading/test/{{ $lexile->id }}'">
                                <i class="fas fa-play me-2"></i> Start Reading Test
                            </button>
                        </div>
                    </div>
                </div>
                @isset($math)
                    <!-- Math Assessment Card -->
                    <div class="col-md-6">
                        <div class="assessment-card card-math p-4">
                            <div class="text-center">
                                <i class="fas fa-square-root-alt card-icon"></i>
                                <h3 class="card-title">Math Factual Fluency Assessment</h3>
                                <p class="card-text">Evaluate your mathematical reasoning and problem-solving abilities
                                    across various topics.</p>
                            </div>

                            <div class="assessment-details">
                                <div class="detail-item">
                                    <i class="fas fa-graduation-cap"></i> Grade: {{ $grade }}
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-clock"></i> Duration: {{ $math->duration ?? '0' }} minutes
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-question-circle"></i> {{ $math->total_question ?? '0' }}
                                    multiple-choice
                                    questions
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button class="btn btn-math btn-assessment"
                                    onclick="window.location.href='/math/test/{{ $math->id ?? '0' }}'">
                                    <i class="fas fa-play me-2"></i> Start Math Test
                                </button>
                            </div>
                        </div>
                    </div>
                @endisset
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function startAssessment(type) {
            if (type === 'reading') {
                alert(
                    'Starting Reading Lexile Assessment...\n\nThis would redirect to the reading test in a real implementation.'
                );
            } else if (type === 'math') {
                alert(
                    'Starting Math Skills Assessment...\n\nThis would redirect to the math test in a real implementation.'
                );
            }
        }
    </script>
</body>

</html>
