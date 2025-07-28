<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .divider:after,
        .divider:before {
            content: "";
            flex: 1;
            height: 1px;
            background: #eee;
        }

        .h-custom {
            height: calc(100% - 73px);
        }

        @media (max-width: 450px) {
            .h-custom {
                height: 100%;
            }
        }
    </style>
</head>

<body>
    <section class="vh-100">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-9 col-lg-6 col-xl-5">
                    <img src="/images/logo-mh.png" class="img-fluid" class="img-fluid" alt="Sample image">
                </div>
                <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                    <form autocomplete="on" id="form-login" action="/login" method="POST">
                        @csrf
                        <div class="form-outline mb-4">
                            <label class="form-label" for="email">Email address</label>
                            <input required id="email" name="email" class="form-control form-control-lg"
                                placeholder="Enter a valid email address" />
                        </div>
                        <div class="form-outline mb-3">
                            <label class="form-label" for="password">Password</label>
                            <input required type="password" name="password" id="password"
                                class="form-control form-control-lg" placeholder="Enter password" />
                        </div>
                        <button class="btn btn-primary btn-lg w-100" type="submit">Login</button>
                    </form>
                </div>
            </div>
        </div>

    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script>
        $(document).ready(function() {
            // $('#form-login').on('submit', function(e) {
            //     e.preventDefault();
            //     login();
            //     console.log("ok")
            // });

        })

        function login() {
            let body = {
                "email": $('#email').val(),
                "password": $('#password').val(),
                "device": "web-lexile",
                "device_id": "--"
            }
            $.ajax({
                url: `https://mhis-hub.mhis.link/api/login`,
                method: 'POST',
                data: JSON.stringify(body),
                contentType: 'application/json',
                dataType: 'json',
                success: function(data) {
                    console.log(data)
                },
                error: function(xhr, status, error) {
                    console.error("Error sending answers:", error);
                    alert("Failed to submit answers. Please try again later.");
                }
            });
        }
    </script>
</body>

</html>
