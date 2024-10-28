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
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Registration Date</th>
                    <th>Registration Time</th>
                    <th>Uploaded Food Item</th>
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
            let res = await axios.get("/admin/client/list/by/customer/info/" + customer_id);
            
            let tableList = $("#tableList");
            tableList.empty(); 

            res.data.data.forEach(function (item, index) {

                let createdAt = new Date(item['created_at']);

                let registrationDate = createdAt.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                });

                let registrationTime = createdAt.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: true
                });

                let fullName = item['lastName'] ? `${item['firstName']} ${item['lastName']}` : item['firstName'];
                let nonPendingFoodCount = item['non_pending_food_count'] || 0;
                let row =`<tr>
                            <td>${index + 1}</td>
                            <td>${item['image'] ? `<img src="/upload/client-profile/small/${item['image']}" width="50" height="50">` : `<img src="/upload/no_image.jpg" width="50" height="50">`}
                            </td>
                            <td>${fullName}</td>
                            <td>${item['email']}</td>
                            <td>${item['mobile']}</td>
                            <td>${registrationDate}</td>
                            <td>${registrationTime}</td>
                            <td>${nonPendingFoodCount}</td>
                         </tr>`;
                tableList.append(row);;
            });

            initializeDataTable();

        } catch (error) {
            handleError(error);
        }finally{
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


