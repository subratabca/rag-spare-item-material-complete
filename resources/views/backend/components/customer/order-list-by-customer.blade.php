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
        let url = window.location.pathname;
        let segments = url.split('/');
        let customer_id = segments[segments.length - 1];

        showLoader();
        try {
            let res = await axios.get("/admin/order/list/by/customer/info/" + customer_id);

            let tableList = $("#tableList");
            tableList.empty(); 

            if (res.data.data.length === 0) {
                tableList.append('<tr><td colspan="8" class="text-center">No Data Found</td></tr>');
            } else {
                res.data.data.forEach(function (item, index) {
                    let row = `
                    <tr>
                    <td>${index + 1}</td>
                    <td>${item['food']['image'] ? `<img src="/upload/food/small/${item['food']['image']}" width="50" height="50">` : `<img src="/upload/no_image.jpg" width="50" height="50">`}</td>
                    <td>${item['food']['name']}</td>
                    <td>${item['order_date']}</td>
                    <td>${item['order_time']}</td>
                    <td>${item['user']['firstName']}</td>
                    <td>
                    <span class="badge ${item['status'] === 'pending' ? 'bg-danger' : 'bg-success'}">${item['status']}</span>
                    </td>
                    <td>
                    <a href="/admin/order/details/${item['id']}" class="btn btn-sm btn-outline-primary"><span class="mdi mdi-eye-circle"></span></a>
                    </td>
                    </tr>`;
                    tableList.append(row); 
                });
            }

            initializeDataTable();

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


