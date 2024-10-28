<div class="card">
    <div class="card-header header-elements">
        <span class="me-2"><h5>Client List Information</h5></span>
    </div>

    <div class="card-datatable table-responsive pt-0">
        <table id="foodTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Sl</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Orders</th>
                    <th>Complains</th>
                    <th>Customers</th>
                    <th>Foods</th>
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
            let res = await axios.get("/admin/clients");

            let tableList = $("#tableList");
            tableList.empty(); 

            res.data.data.forEach(function (item, index) {
                let fullName = item['lastName'] ? `${item['firstName']} ${item['lastName']}` : item['firstName'];
                let row = `
                    <tr>
                        <td>${index + 1}</td>

                        <td>${item['image'] ? `<img src="/upload/client-profile/small/${item['image']}" width="50" height="50">` : `<img src="/upload/no_image.jpg" width="50" height="50">`}
                        </td>

                        <td><a href="/admin/client/details/${item['id']}">${fullName}</a></td>

                        <td>
                           ${item['total_orders'] > 0 ? `<a href="/admin/order/list/by/client/${item['id']}" class="badge bg-success">${item['total_orders']}</a>` : item['total_orders']}
                        </td>

                        <td>
                            ${item['total_complaints'] > 0 ? `<a href="/admin/complain/list/by/client/${item['id']}" class="badge bg-success">${item['total_complaints']}</a>` : item['total_complaints']}
                        </td>

                        <td>
                           ${item['total_customers'] > 0 ? `<a href="/admin/customer/list/by/client/${item['id']}" class="badge bg-success">${item['total_customers']}</a>` : item['total_customers']}
                        </td>

                        <td>${item['non_pending_food_count'] > 0 ? `<a href="/admin/food/list/by/client/${item['id']}" class="badge bg-success">${item['non_pending_food_count']}</a>` : item['non_pending_food_count']}
                        </td>

                        <td>
                            <span class="badge ${item['is_email_verified'] === 1 ? 'bg-success' : 'bg-danger'}">
                                ${item['is_email_verified'] === 1 ? 'Active' : 'Inactive'}
                            </span>
                        </td>
                        
                        <td>
                            ${item['is_email_verified'] === 1 ? `
                                <button data-id="${item['id']}" data-status="0" title="Inactive" class="toggleStatusBtn btn btn-sm btn-outline-danger">
                                    <span class="mdi mdi-thumb-down"></span>
                                </button>
                            ` : `
                                <button data-id="${item['id']}" data-status="1" title="Active" class="toggleStatusBtn btn btn-sm btn-outline-success">
                                    <span class="mdi mdi-thumb-up"></span>
                                </button>
                            `}
                            <button data-id="${item['id']}" class="btn deleteBtn btn-sm btn-outline-danger"><span class="mdi mdi-trash-can-outline"></span></button>
                        </td>
                    </tr>`;
                tableList.append(row);
            });

            $('.toggleStatusBtn').on('click', function() {
                let clientId = $(this).data('id');
                let newStatus = $(this).data('status');
                toggleClientStatus(clientId, newStatus);
            });

            initializeDataTable();
            attachEventListeners();

        } catch (error) {
            handleError(error);
        } finally {
            hideLoader();
        }
    }

    async function toggleClientStatus(clientId, newStatus) {
        showLoader();
        try {
            let res = await axios.post(`/admin/update/client/account/${clientId}`, {
                is_email_verified: newStatus
            }, {
                headers: {
                    'Cache-Control': 'no-cache'
                }
            });

            successToast(res.data.message);
            window.location.href = '/admin/client-list';

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
        $('.deleteBtn').on('click', function () {
            let id = $(this).data('id');
            $("#deleteID").val(id);
            $("#delete-modal").modal('show');
        });
    }

    function handleError(error) {
        if (error.response) {
            if (error.response.status === 404) {
                errorToast(error.response.data.message || "Client not found with id");
            } else if (error.response.status === 500) {
                errorToast(error.response.data.error || "An internal server error occurred.");
            } else {
                errorToast("Request failed!");
            }
        } else {
            errorToast("Request failed!");
        }
    }
</script>


