@extends('layout.main')
@section('styles')
    <style>
        .task-list li {
            list-style: none;
        }

        .task-title {
            font-weight: bold;
            font-size: 20px;
        }

        .page-title h2 {
            display: inline-block;
        }

        .task-list li.completed {
            background: #d0d0d0;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="page-title col-sm-12 my-4">
            <h2>Tasks</h2>
            <button type="button" class="btn btn-sm btn-secondary float-right" data-toggle="modal"
                    data-target="#create_modal">
                <i class="fas fa-plus"></i> Create
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <ul class="task-list list-group">
                @foreach($tasks as $task)
                    <li class="list-group-item @if($task->completed) completed @endif" data-id="{{ $task->id }}">
                        <button type="button" class="btn btn-sm btn-info toggle-task">
                            @if($task->completed)
                                <i class="fas fa-check-square"></i>
                            @else
                                <i class="fas fa-minus-square"></i>
                            @endif
                        </button>
                        <span class="task-title">{{ $task->title }}</span>
                        <span class="btn-group btn-group-sm float-right">
                            <button type="button" class="btn btn-sm btn-success" data-toggle="modal"
                                    data-target="#edit_modal" data-action="{{ route('tasks.update', $task->id) }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" data-toggle="modal"
                                    data-target="#delete_modal">
                                <i class="fas fa-trash"></i>
                            </button>
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="modal" id="create_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form method="post" action="{{ route('tasks.store') }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Task</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="create_title">Title:</label>
                            <input id="create_title" class="form-control" name="title"/>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Task</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal" id="edit_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form>
                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Task</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_title">Title:</label>
                            <input id="edit_title" class="form-control" name="title"/>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                    <div class="modal-loading mx-auto my-5" style="width: 80px;display: none;">
                        <i class="fas fa-spinner fa-spin fa-5x"></i>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal" id="delete_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form>
                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Task</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete the task?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                    <div class="modal-loading mx-auto my-5" style="width: 80px;display: none;">
                        <i class="fas fa-spinner fa-spin fa-5x"></i>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('footer_scripts')
    <script type="text/javascript">
        $(document).ready(function () {

            // handle edit form loading
            $('#edit_modal').on('shown.bs.modal', function (e) {

                var $modal = $(this);
                var $button = $(e.relatedTarget);
                var $editForm = $modal.find('form');
                //grab related data
                var task_id = $button.closest('li').attr('data-id');
                console.log(task_id);
                $editForm.attr('action', $button.attr('data-action'));

                // loading
                var $loader = $editForm.find('.modal-loading');
                var $contents = $editForm.find('.modal-header, .modal-body, .modal-footer');
                $loader.show();
                $contents.hide();

                // get form data
                $.ajax({
                    url: 'tasks/' + task_id,
                    success: function (response) {
                        // fill form fields, show contents and hide loader
                        $editForm.find('input[name="title"]').val(response.task.title);
                        $loader.hide();
                        $contents.show();
                    },
                    error: function (xhr) {
                        $('body > .container').prepend('<div class="alert alert-danger mt-2">Error Retrieving Task'
                            + '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
                            + '</div>');

                        $modal.modal('hide');
                    }
                })

                // listen for submit, show loaders
                $editForm.submit(function (e) {

                    e.preventDefault();

                    var $loader = $editForm.find('.modal-loading');
                    var $contents = $editForm.find('.modal-header, .modal-body, .modal-footer');
                    $loader.show();
                    $contents.hide();

                    // use ajax as put is not supported by html forms alone
                    $(this).ajaxSubmit({
                        'method': 'PUT',
                        'success': function (response) {
                            console.log(response);
                            if (response.status == 'success') {
                                window.location.href = window.location.href;
                            }
                        },
                        'error': function (xhr) {
                            console.log(JSON.parse(xhr.responseText));
                            $('#edit_modal').modal('hide');
                            $('body > .container').prepend('<div class="alert alert-danger mt-2">' + JSON.parse(xhr.responseText).message
                                + '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
                                + '</div>')
                        }
                    });
                });
            }).on('hidden.bs.modal', function (e) {
                // clear inputs
                $(this).find('[name="title"]').val('');
                $(this).find('form').off('submit');
            });

            // handle task completion
            $('.toggle-task').click(function (e) {
                var $button = $(this);
                $button.find('i').removeClass('fa-check-square, fa-minus-square').addClass('fa-spinner fa-spin');
                var $listItem = $button.closest('li');
                var task_id = $listItem.attr('data-id');
                var completed = $listItem.hasClass('completed') ? 0 : 1;
                $.ajax({
                    'method': 'PUT',
                    url: '/tasks/' + task_id + '/' + completed,
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (completed) {
                            $button.find('i').removeClass('fa-spinner fa-spin').addClass('fa-check-square');
                            $listItem.addClass('completed');
                        } else {
                            $button.find('i').removeClass('fa-spinner fa-spin').addClass('fa-minus-square');
                            $listItem.removeClass('completed');
                        }
                    },
                    error: function (xhr) {
                        // revert to before loading
                        if (completed) {
                            $button.find('i').removeClass('fa-spinner fa-spin').addClass('fa-minus-square');
                        } else {
                            $button.find('i').removeClass('fa-spinner fa-spin').addClass('fa-check-square');
                        }
                    }
                });
            });

            $('#delete_modal').on('shown.bs.modal', function (e) {
                var $modal = $(this);
                var $button = $(e.relatedTarget);
                var task_id = $button.closest('li').attr('data-id');
                var $loader = $modal.find('.modal-loading');
                var $contents = $modal.find('.modal-header, .modal-body, .modal-footer');

                $modal.find('button.btn-danger').click(function (e) {
                    $loader.show();
                    $contents.hide();
                    $.ajax({
                        url: '/tasks/' + task_id,
                        method: 'delete',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            window.location.href = window.location.href;
                        },
                        error: function (xhr) {
                            $modal.modal('hide');
                            $loader.hide();
                            $contents.show();
                            $('body > .container').prepend('<div class="alert alert-danger mt-2">Error Deleting Task'
                                + '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
                                + '</div>')
                        }
                    });
                });
            }).on('hidden.bs.modal', function (e) {
                // unset the event listener to prevent multiple delete calls
                $(this).find('button.btn-danger').off('click');
            });
        });
    </script>
@endsection
