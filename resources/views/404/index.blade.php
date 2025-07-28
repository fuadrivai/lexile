<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .error-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background-color: #f8f9fa;
    }

    .error-content {
        text-align: center;
    }

    .error-content h1 {
        font-size: 6rem;
        font-weight: bold;
        margin-bottom: 1rem;
    }

    .error-content p {
        font-size: 1.5rem;
        margin-bottom: 2rem;
    }

    .lottie-animation {
        max-width: 400px;
        margin-bottom: 2rem;
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.9.6/lottie.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<div class="error-container">
    <div class="lottie-animation"></div>
    <div class="error-content">
        <h1>404</h1>
        <p>Oops! Something went wrong!</p>
        <a href="#" class="btn btn-primary" onclick="window.history.back(); return false;">Go Back</a>
    </div>
</div>
