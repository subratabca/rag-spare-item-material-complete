<div class="card">
    <div class="card-header header-elements">
        <span class="me-2"><h5>Customer List Information</h5></span>
    </div>

    <div class="card-datatable table-responsive pt-0">
        <table id="foodTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Sl</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Orders</th>
                    <th>Complains</th>
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
            let res = await axios.get("/client/customers");

            let tableList = $("#tableList");
            tableList.empty(); 

            res.data.data.forEach(function (item, index) {
                let fullName = item['lastName'] ? `${item['firstName']} ${item['lastName']}` : item['firstName'];

                let row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                           ${item['image'] ? `<img src="/upload/user-profile/small/${item['image']}" width="50" height="50">` : `<img src="/upload/no_image.jpg" width="50" height="50">`}
                        </td>

                        <td><a href="/client/customer/details/${item['id']}">${fullName}</a></td>

                        <td>${item['email']}</td>
                        <td>${item['mobile']}</td>

                        <td>${item['orders_count'] > 0 ? `<a href="/client/order/list/by/customer/${item['id']}" class="badge bg-success">${item['orders_count']}</a>` : item['orders_count']}
                        </td>

                        <td>${item['complains_count'] > 0 ? `<a href="/client/complain/list/by/customer/${item['id']}" class="badge bg-success">${item['complains_count']}</a>` : item['complains_count']}
                        </td>
                    </tr>`;
                
                tableList.append(row);
            });

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
