<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task App</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container my-5">
    <h1 class="text-center mb-4">Task List</h1>

    <div class="row mb-4">
        <div class="col-md-8 mx-auto">
            <div class="input-group">
                <input type="text" id="title" class="form-control" placeholder="Enter Task Title" aria-label="Enter Task Title">
                <button class="btn btn-primary" onclick="addTask()">Add Task</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10 mx-auto">
            <table id="taskTable" class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTaskForm">
                    <input type="hidden" id="editTaskId">
                    <div class="mb-3">
                        <label for="editTitle" class="form-label">Title</label>
                        <input type="text" id="editTitle" class="form-control" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" id="editStatus" class="form-check-input">
                        <label for="editStatus" class="form-check-label">Completed</label>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="updateTask()">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        loadTasks();
    });

    function loadTasks() {
        $('#taskTable').DataTable({
            "ajax": {
                "url": "tasks",
                "dataSrc": "",
                "error": function(xhr, error, code) {
                    console.log("AJAX Error: ", error); 
                    alert("Could not load data. Check the console for more details.");
                }
            },
            "columns": [
                { "data": "id" },
                { "data": "title" },
                { "data": "status", "render": function(data, type, row) {
                    return `<input type="checkbox" ${data == 1 ? 'checked' : ''} disabled>`;
                }},
                { "data": null, "render": function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-secondary me-1" onclick="editTask(${row.id})">Edit</button>
                        <button class="btn btn-sm btn-warning me-1" onclick="toggleStatus(${row.id}, ${row.status})">
                            ${row.status == 1 ? 'Mark Pending' : 'Mark Completed'}
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteTask(${row.id})">Delete</button>
                    `;
                }}
            ],
            "destroy": true,
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": false,
            "info": false,
            "autoWidth": false,
            "responsive": true
        });
    }

    function addTask() {
        var title = $('#title').val();
        if (title === "") {
            alert("Please enter a title.");
            return;
        }
        $.post('tasks', { title: title }, function(response) {
            $('#taskTable').DataTable().ajax.reload();
            $('#title').val('');
        });
    }

    function deleteTask(id) {
        if (!confirm("Are you sure you want to delete this task?")) return;
        $.ajax({
            url: `/tasks/${id}`,
            type: 'DELETE',
            success: function(response) {
                $('#taskTable').DataTable().ajax.reload();
            },
            error: function(xhr, status, error) {
                console.error("Error deleting task:", error);
                alert("Failed to delete task. Please try again.");
            }
        });
    }

    function editTask(id) {
        $.get(`tasks/${id}`, function(task) {
            $('#editTaskId').val(task.id);
            $('#editTitle').val(task.title);
            $('#editStatus').prop('checked', task.status == 1);
            $('#editTaskModal').modal('show');
        });
    }

    function updateTask() {
        var id = $('#editTaskId').val();
        var title = $('#editTitle').val();
        var status = $('#editStatus').is(':checked') ? 1 : 0; 

        $.ajax({
            url: `/tasks/${id}`,
            type: 'PUT',
            data: { title: title, status: status },
            success: function(response) {
                $('#taskTable').DataTable().ajax.reload();
                $('#editTaskModal').modal('hide');
            },
            error: function(xhr, status, error) {
                console.error("Error updating task:", error);
                alert("Failed to update task. Please try again.");
            }
        });
    }

    function toggleStatus(id, currentStatus) {
        var newStatus = currentStatus == 1 ? 0 : 1;
        $.post(`tasks/update-status/${id}`, { status: newStatus }, function(response) {
            $('#taskTable').DataTable().ajax.reload();
        });
    }
</script>

</body>
</html>
