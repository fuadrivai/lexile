<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Grade Level | Reading Lexile Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            font-family: 'Segoe UI', sans-serif;
        }

        .container {
            padding: 60px 20px;
        }

        .grade-card {
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 40px 0;
            text-align: center;
            background-color: white;
            transition: all 0.3s ease-in-out;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .grade-card:hover {
            transform: translateY(-5px);
            background-color: #0d6efd;
            color: white;
        }

        .grade-card h5 {
            font-size: 1.5rem;
            margin: 0;
            font-weight: 600;
        }

        .page-title {
            text-align: center;
            margin-bottom: 40px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="page-title">
            <h1 class="display-5 fw-bold">Select Your Grade Level</h1>
            <p class="lead text-muted">Start your personalized reading level test</p>
        </div>

        <div class="row g-4">
            @foreach ($grades as $grade)
                <div class="col-6 col-sm-4 col-md-3">
                    <a style="text-decoration: none; color: inherit;"
                        href="/reading/test?grade_level={{ $grade }}">
                        <div class="card grade-card">
                            <div class="card-body">
                                <h5> Grade {{ $grade }}</h5>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
