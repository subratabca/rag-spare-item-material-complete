@extends('frontend.components.dashboard.dashboard-master')
@section('dashboard-content')

<div class="card">
    <div class="card-header header-elements">
        <span class="me-2"><h5>Complaint List</h5></span>
    </div>

    <div class="card-datatable table-responsive pt-0">
        <table id="foodTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Sl</th>
                    <th>Image</th>
                    <th>Item Name</th>
                    <th>Complaint Date</th>
                    <th>Provider Name</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tableList">
                
            </tbody>
        </table>
    </div>
</div>
@endsection
<script>
    document.addEventListener("DOMContentLoaded", function () {
        getList(); 
    });

    async function getList() {
        showLoader();
        try {
            let res = await axios.get("/user/complains");

            let tableList = $("#tableList");
            tableList.empty(); 

            res.data.data.forEach(function (item, index) {
                let row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            ${item.food.image ? 
                            `<img src="/upload/food/small/${item.food.image}" width="50" height="50">` : 
                            `<img src="/upload/no_image.jpg" width="50" height="50">`}
                        </td>
                        <td>${item['food']['name']}</td>
                        <td>${item['cmp_date']}</td>
                        <td>${item['food']['user']['firstName']}</td> 
                        <td>
                            <span class="badge ${item.status === 'pending' ? 'bg-danger' : 'bg-success'}">
                            ${item.status}
                            </span>
                        </td>
                        <td>
                            <a href="/user/complain-details/${item['id']}" class="btn btn-sm btn-info">Details</a>

                            ${(item.status === 'under-review') ? 
                            `<a href="/user/complain/reply/${item['id']}" class="btn btn-sm btn-danger">Reply</a>` : ''}

                            ${item['status'] === 'under-review' 
                            ? `<button data-id="${item['id']}" class="btn replyBtn btn-sm btn-outline-danger">Modal Reply</button>`
                            : ''}
                        </td>
                    </tr>`;
                tableList.append(row);
            });

            initializeDataTable();
            attachEventListeners();

        } catch (error) {
            handleError(error);
        } finally {
            hideLoader();
        }
    }

    function initializeDataTable() {
        if ($.fn.DataTable.isDataTable('#foodTable')) {
            $('#foodTable').DataTable().destroy();
        }

        $('#foodTable').DataTable({
            "paging": true,
            "serverSide": false, 
            "autoWidth": false,
            "ordering": true,
            "searching": true, 
            "lengthMenu": [10, 25, 50, 100], 
            "pageLength": 10, 
        });
    }

    function attachEventListeners() {
        $('.replyBtn').on('click', function () {
            let id = $(this).data('id');
            $("#complainID").val(id);
            $("#reply-modal").modal('show');
        });
    }
    
    function handleError(error) {
        if (error.response) {
            if (error.response.status === 500) {
                errorToast(error.response.data.error || "An internal server error occurred.");
            } else {
                errorToast("Request failed!");
            }
        } else {
            errorToast("Request failed!");
        }
    }

</script>

