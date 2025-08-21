@extends('layouts.main-layout')

@section('content-child')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Student Test Record</div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label for="">Choose Grade 1 to 12</label>
                                <select class="form-control" id="grade" name="grade">
                                    <option value="" selected disabled>Choose grade</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        @if ($i <= 12)
                                            <option value="{{ $i }}">Grade {{ $i }}</option>
                                        @endif
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label for="">Choose Subject</label>
                                <select class="form-control" id="subject" name="subject">
                                    <option value="all">All Subject</option>
                                    <option value="math">Math Factual Fluency</option>
                                    <option value="lexile">Reading Lexile</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-12" style="padding-top:36px !important;">
                            <button class="btn btn-black btn-primary" id="btn-search" type="button">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row d-none row-table">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="align-self: flex-end;">
                    <button type="button" onclick="exportExcel()" class="btn btn-secondary" id="btn-export">Export Excel <i
                            class="fa fa-download"></i></button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tbl-answer" class="display table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th rowspan="2">Name</th>
                                    <th rowspan="2">Topic</th>
                                    <th rowspan="2">Subject</th>
                                    <th colspan="3" class="text-center">Total</th>
                                    <th rowspan="2">Score</th>
                                    <th rowspan="2">Performance</th>
                                    <th rowspan="2">Examination Date</th>
                                    <th rowspan="2">#</th>
                                </tr>
                                <tr>
                                    <th>Question</th>
                                    <th>Answered</th>
                                    <th>Correct</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content-script')
    <!-- Datatables -->
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="../assets/js/plugin/moment/moment.min.js"></script>
    <script src="../assets/js/plugin/jquery-loading-overlay/loadingoverlay.min.js"></script>
    <script>
        let answers = [];
        $(document).ready(function() {
            tblAnswer = $("#tbl-answer").DataTable({
                ordering: false,
                data: answers,
                columns: [{
                        data: "student.name",
                        defaultContent: "--",
                        mRender: function(data, type, full) {
                            return `<a href="/admin/history/answere/${full.id}">
                                ${data}<br>
                                <small>${full.student.email}</small><br>
                                <small>Grade ${full.grade}</small>
                            </a>`
                        }
                    },
                    {
                        data: "topic",
                        defaultContent: "--"
                    },
                    {
                        data: "subject",
                        defaultContent: "--",
                        mRender: function(data, type, full) {
                            let badge = "";
                            let text = "";
                            if (data == "math") {
                                badge = "badge-primary";
                                text = "Math Factual Fluency";
                            } else if (data == "lexile") {
                                badge = "badge-secondary";
                                text = "Reading Lexile";
                            } else {
                                badge = "badge-danger";
                                text = "--";
                            }
                            return `<span class="badge ${badge}">${text}</span>`
                        }
                    },
                    {
                        data: "total_questions",
                        defaultContent: "--",
                    },
                    {
                        data: "total_answered",
                        defaultContent: "--"
                    },
                    {
                        data: "correct_answers",
                        defaultContent: "--"
                    },
                    {
                        data: "score",
                        defaultContent: "--",
                        mRender: function(data, type, full) {
                            return `${data} %`
                        }
                    },
                    {
                        data: "performance",
                        defaultContent: "--"
                    },
                    {
                        data: "created_at",
                        defaultContent: "--",
                        mRender: function(data, full, type) {
                            return moment(data).format("DD MMMM YYYY HH:mm:ss")
                        }
                    },
                    {
                        data: "id",
                        defaultContent: "--",
                        mRender: function() {
                            return `<a href="" class="btn btn-sm btn-primary btn-show"><i class="far fa-eye"></i></a>`
                        }
                    },
                ]
            });

            $('#btn-search').on('click', function() {
                $('.row-table').LoadingOverlay("show", {
                    image: "",
                    fontawesome: "fa fa-cog fa-spin",
                    background: "rgba(165, 190, 100, 0.5)"
                });
                let grade = $('#grade').val();
                let subject = $('#subject').val();
                if (grade) {
                    let body = {
                        grade,
                        subject
                    }
                    getAnswere(body)
                } else {
                    console.log('no')
                }
            })
        })

        function getAnswere(body) {
            ajax(body, `{{ URL::to('/admin/history/grade') }}`, "GET",
                function(json) {
                    answers = json;
                    reloadJsonDataTable(tblAnswer, answers)
                    $('.row-table').removeClass('d-none')
                    $('.row-table').LoadingOverlay("hide");
                },
                function(json) {
                    $('.row-table').LoadingOverlay("hide");
                })
        }

        function exportExcel() {
            $('.row-table').LoadingOverlay("show", {
                image: "",
                fontawesome: "fa fa-cog fa-spin",
                background: "rgba(165, 190, 100, 0.5)"
            });
            let gradeId = $('#grade').val();
            let subject = $('#subject').val();
            window.location.href = `/export-answer/${gradeId}/${subject}`;
            $('.row-table').LoadingOverlay("hide");
        }
    </script>
@endsection
