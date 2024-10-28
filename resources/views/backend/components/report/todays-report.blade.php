<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="me-1">
                        <p class="text-heading mb-2">Today's Total Order</p>
                        <div class="d-flex align-items-center">
                            <h4 class="mb-2 me-1 display-6" id="total-orders">0</h4>
                        </div>
                        <p class="mb-0">Total Users</p>
                    </div>
                    <div class="avatar">
                        <div class="avatar-initial bg-label-primary rounded">
                            <div class="mdi mdi-account-outline mdi-24px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="me-1">
                        <p class="text-heading mb-2">Completed Order</p>
                        <div class="d-flex align-items-center">
                            <h4 class="mb-2 me-1 display-6" id="completed-orders">0</h4>
                        </div>
                        <p class="mb-0">Last week analytics</p>
                    </div>
                    <div class="avatar">
                        <div class="avatar-initial bg-label-danger rounded">
                            <div class="mdi mdi-account-plus-outline mdi-24px scaleX-n1"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="me-1">
                        <p class="text-heading mb-2">Pending Order</p>
                        <div class="d-flex align-items-center">
                            <h4 class="mb-2 me-1 display-6" id="pending-orders">0</h4>
                        </div>
                        <p class="mb-0">Last week analytics</p>
                    </div>
                    <div class="avatar">
                        <div class="avatar-initial bg-label-success rounded">
                            <div class="mdi mdi-account-check-outline mdi-24px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="me-1">
                        <p class="text-heading mb-2">Cancel Order</p>
                        <div class="d-flex align-items-center">
                            <h4 class="mb-2 me-1 display-6" id="cancel-orders">0</h4>
                        </div>
                        <p class="mb-0">Last week analytics</p>
                    </div>
                    <div class="avatar">
                        <div class="avatar-initial bg-label-warning rounded">
                            <div class="mdi mdi-account-search mdi-24px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="card">
    <div class="card-header header-elements">
        <span class="me-2"><h5>Order List Information</h5></span>
    </div>

    <div class="card-datatable table-responsive pt-0">
        <table id="foodTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Sl</th>
                    <th>Image</th>
                    <th>Food Nmae</th>
                    <th>Order Date</th>
                    <th>Order Time</th>
                    <th>Order By</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tableList">

            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        getList();
    });

    async function getList() {
        showLoader();
        try {
            let res = await axios.get("/admin/todays/order/information");

            document.getElementById('total-orders').innerText = res.data.total_orders;
            document.getElementById('completed-orders').innerText = res.data.total_completed_orders;
            document.getElementById('pending-orders').innerText = res.data.total_pending_orders;
            document.getElementById('cancel-orders').innerText = res.data.total_canceled_orders;


            let tableList = $("#tableList");
            tableList.empty(); 

            res.data.data.forEach(function (item, index) {
                let row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item['food']['image'] ? `<img src="/upload/food/small/${item['food']['image']}" width="50" height="50">` : `<img src="/upload/no_image.jpg" width="50" height="50">`}
                        </td>
                        <td>${item['food']['name']}</td>
                        <td>${item['order_date']}</td>
                        <td>${item['order_time']}</td>
                        <td>${item['user']['firstName']}</td>
                        <td>
                            <span class="badge ${item['status'] === 'pending' ? 'bg-danger' : 'bg-success'}">${item['status']}</span>
                        </td>
                        <td>
                            <a  href="/admin/order/details/${item['id']}" class="btn btn-sm btn-outline-primary"><span class="mdi mdi-eye-circle"></span>
                            </a>

                        </td>
                    </tr>`;

                tableList.append(row);
            });
            initializeDataTable();

        } catch (error) {
            handleError(error);
        }finally {
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