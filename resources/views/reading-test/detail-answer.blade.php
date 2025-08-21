<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reading Test - Grade {{ $answer->grade }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .container {
            display: flex;
            flex-direction: row;
            margin-top: 40px;
        }

        .passage-box {
            width: 40%;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            height: 90vh;
            overflow-y: auto;
            position: sticky;
            top: 20px;
        }

        .quiz-box {
            width: 60%;
            padding: 20px 40px;
        }

        .question-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .option-label {
            display: block;
            margin-bottom: 10px;
        }

        .btn-nav {
            margin-top: 20px;
        }

        .result-box {
            margin-top: 30px;
        }

        #introForm {
            max-width: 500px;
            margin: 60px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>

    <div class="container" id="mainContent">
        <div class="passage-box">
            <h4>Topic: <span>{{ $answer->topic }}</span></h4>
            <p><strong>Lexile Level:</strong> {{ $answer->lexile_level }} L</p>
            <hr>
            <p id="passage" style="text-align: justify">{{ $answer->passage->passage }}</p>
        </div>

        <div class="quiz-box">
            <form id="quizForm">
                <div id="question-container">
                    @foreach ($answer->passage->questions as $i => $q)
                        @php
                            $color = '';
                            $selected = '';
                        @endphp
                        @foreach ($answer->details as $d)
                            @if ($d->question_id === $q->id)
                                @php
                                    $color = !$d->is_correct ? 'alert alert-danger' : '';
                                    $selected = $d->selected_option;
                                @endphp
                            @endif
                        @endforeach
                        <div class="question-card mb-4 {{ $color }}">
                            <h5>Question {{ $loop->iteration }} of {{ count($answer->passage->questions) }}</h5>
                            <p><strong>{{ $loop->iteration }}. {{ $q->question }}</strong></p>
                            <?php $correct = $q->correct_answer; ?>
                            @foreach (['a', 'b', 'c', 'd'] as $letter)
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input {{ $selected == strtoupper($letter) ? 'checked' : '' }} disabled
                                            question-id="{{ $q->id }}" class="form-check-input" type="radio"
                                            name="q{{ $q->id }}" value="{{ strtolower($letter) }}">
                                        @if ($correct == strtoupper($letter))
                                            <span class="text-success fw-bold">{{ strtoupper($letter) }}.
                                                {{ $q['option_' . $letter] }}</span>
                                        @else
                                            <span>{{ strtoupper($letter) }}. {{ $q['option_' . $letter] }}</span>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </form>
            <div class="result-box" id="resultBox" style="display:none;"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
