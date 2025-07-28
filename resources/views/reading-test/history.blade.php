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
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="">Choose Grade 1 to 12</label>
                                <div class="input-group">
                                    <select class="form-control" id="grade" name="grade">
                                        <option value="" selected disabled>Choose grade</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            @if ($i <= 12)
                                                <option value="{{ $i }}">Grade {{ $i }}</option>
                                            @endif
                                        @endfor
                                    </select>
                                    <button class="btn btn-black btn-primary" id="btn-search" type="button">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tbl-answer" class="display table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th rowspan="2">Name</th>
                                    <th rowspan="2">Topic</th>
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
                let gradeId = $('#grade').val();
                if (gradeId) {
                    getAnswere(gradeId)
                } else {
                    console.log('no')
                }
            })
        })

        function getAnswere(gradeId) {
            ajax(null, `{{ URL::to('/admin/history/grade/${gradeId}') }}`, "GET",
                function(json) {
                    answers = json;
                    reloadJsonDataTable(tblAnswer, answers)
                })
        }
    </script>
@endsection
