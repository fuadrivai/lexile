<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reading Test - Grade {{ $grade }}</title>
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

    <div class="container" id="mainContent" style="display: none;">
        <div class="passage-box">
            <h4>Topic: <span id="topic"></span></h4>
            <p><strong>Lexile Level:</strong> <span id="lexile-level"></span> L</p>
            <hr>
            <p id="passage" style="text-align: justify"></p>
        </div>

        <div class="quiz-box">
            <div class="text-end text-muted mb-3">
                Time Left: <span id="timer"></span>
            </div>
            <form id="quizForm">
                <div id="question-container"></div>
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" id="prev" class="btn btn-secondary">Previous</button>
                    <button type="button" id="next" class="btn btn-primary">Next</button>
                </div>
            </form>
            <div class="result-box" id="resultBox" style="display:none;"></div>
        </div>
    </div>

    <div id="introForm">
        <h3 class="mb-4">Start Reading Test - Grade {{ $grade }}</h3>
        <form id="startForm">
            <div class="mb-3">
                <label for="studentName" class="form-label">Your Name</label>
                <input type="text" class="form-control" id="studentName" required>
            </div>
            <div class="mb-3">
                <label for="studentEmail" class="form-label">Email</label>
                <input type="email" class="form-control" id="studentEmail" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Start Test</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script>
        let allQuestions = [];
        let currentPage = 1;
        const perPage = 5;
        let userAnswers = {};
        let passage = null;
        let timerInterval;
        let answers = {
            details: [],
        }

        let grade = {{ $grade }};
        $(document).ready(function() {
            $('#startForm').on('submit', function(e) {
                e.preventDefault();
                $('#introForm').hide();
                startTimer();
                $('#mainContent').show();
                // renderQuestions(currentPage);
            });

            $('#prev').on('click', function() {
                currentPage--;
                renderQuestions();
            });

            $('#next').on('click', function() {
                if (currentPage == Math.ceil(allQuestions.length / perPage)) {
                    sendAnswers();
                    return;
                }
                currentPage++;
                renderQuestions();
            });
            $('#question-container').on('change', '.form-check-input', function() {
                const questionId = $(this).attr('question-id');
                const question = allQuestions.find(q => q.id == questionId);
                const exists = answers.details.some(detail => detail.question.id === parseInt(questionId));
                if (!exists) {
                    answers.details.push({
                        question: question,
                        selected_option: $(this).val(),
                        selected_option_text: question[`option_${$(this).val().toLowerCase()}`],
                    });
                } else {
                    const detail = answers.details.find(detail => detail.question.id === parseInt(
                        questionId));
                    detail.selected_option = $(this).val();
                    detail.selected_option_text = question[`option_${$(this).val().toLowerCase()}`];
                }
            })
            // Fetch questions from the API
            $.ajax({
                url: `/api/grade/${grade}/question`,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    passage = data;
                    $('topic').text(data.topic);
                    $('#lexile-level').text(data.lexile_level);
                    $('#passage').text(data.passage);
                    allQuestions = data.questions;
                    if (allQuestions.length === 0) {
                        alert("No questions available for this grade.");
                        return;
                    }
                    renderQuestions()
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching questions:", error);
                    alert("Failed to load questions. Please try again later.");
                }
            });
        });

        function renderQuestions() {
            const start = (currentPage - 1) * perPage;
            const questions = allQuestions.slice(start, start + perPage);

            $('#question-container').empty();

            questions.forEach((q, i) => {
                const qNum = start + i + 1;
                const saved = userAnswers[q.id] || '';
                const detail = answers.details.find(d => d.question.id === parseInt(q.id));
                let selected = (detail?.selected_option ?? "").toUpperCase();
                const options = ['a', 'b', 'c', 'd'].map((letter, i) => `
                    <div class="form-check">
                        <label class="form-check-label">
                            <input question-id="${q.id}" class="form-check-input" type="radio" name="q${q.id}" value="${letter.toUpperCase()}"
                                ${selected === letter.toUpperCase() ? 'checked' : ''}>
                            <span>${letter.toUpperCase()}. ${q['option_' + letter]}</span>
                        </label>
                    </div>`).join('');

                $('#question-container').append(`
                    <div class="question-card mb-4">
                        <h5>Question ${start + i + 1} of ${allQuestions.length}</h5>
                        <p><strong>${qNum}. ${q.question}</strong></p>
                        ${options}
                    </div>
                `);
            });

            $('#prev').prop('disabled', currentPage === 1);
            $('#next').text(currentPage === Math.ceil(allQuestions.length / perPage) ? 'Submit' : 'Next');
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function startTimer() {
            let duration = (passage.duration || 30) * 60; // Default to 30 minutes if not provided
            timerInterval = setInterval(() => {
                const minutes = Math.floor(duration / 60);
                const seconds = duration % 60;
                $('#timer').text(`${minutes}:${seconds.toString().padStart(2, '0')}`);
                if (--duration < 0) {
                    clearInterval(timerInterval);
                    // savePageAnswers();
                    // finishQuiz(true);
                }
            }, 1000);
        }

        function sendAnswers() {
            clearInterval(timerInterval);
            let user = {
                name: $('#studentName').val().trim(),
                email: $('#studentEmail').val().trim()
            }
            const startTime = timeStringToSeconds(`${passage.duration}:00`);
            const endTime = timeStringToSeconds($('#timer').text());
            answers.user = user;
            answers.grade = grade;
            answers.passage_id = passage.id;
            answers.total_questions = allQuestions.length;
            answers.total_time = Math.abs(startTime - endTime);

            $.ajax({
                url: `/api/grade/${grade}/question`,
                method: 'POST',
                data: JSON.stringify(answers),
                contentType: 'application/json',
                dataType: 'json',
                success: function(data) {
                    $('#quizForm').hide();
                    $('#resultBox').show();
                    $('#resultBox').html(`
                        <div class="alert alert-success">
                            <h4 class="alert-heading">Test Completed</h4>
                            <p>You answered <strong>${data.total_answered} out of ${data.total_questions}</p>
                            <p>Correct Answer : <strong>${data.correct_answers}%</strong></p>
                            <p>Score: <strong>${Math.round(data.score)}%</strong></p>
                            <p>Estimated Lexile Score: <strong>${data.estimated_lexile}L</strong></p>
                            <p>Performance Level: <strong>${data.performance}</strong></p>
                        </div>
                    `);
                },
                error: function(xhr, status, error) {
                    console.error("Error sending answers:", error);
                    alert("Failed to submit answers. Please try again later.");
                }
            });
        }

        function timeStringToSeconds(timeStr) {
            const [minutes, seconds] = timeStr.split(":").map(Number);
            return minutes * 60 + seconds;
        }
    </script>
</body>

</html>
