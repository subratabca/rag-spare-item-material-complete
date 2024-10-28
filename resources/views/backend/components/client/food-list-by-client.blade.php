<div class="card">
    <div class="card-header header-elements">
        <span class="me-2"><h5>Food List Information</h5></span>
    </div>

    <div class="card-datatable table-responsive pt-0">
        <table id="foodTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Sl</th>
                    <th>Image</th>
                    <th>Item Name</th>
                    <th>Expire Date</th>
                    <th>Collection Date</th>
                    <th>Collection Time</th>
                    <th>Provider</th>
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
        let client_id = segments[segments.length - 1];

        showLoader();
        try {
            let res = await axios.get("/admin/food/list/by/client/info/" + client_id);

            let tableList = $("#tableList");
            tableList.empty(); 

            res.data.data.forEach(function (item, index) {

                let formattedExpireDate = formatDate(item['expire_date']);
                let formattedCollectionDate = formatDate(item['collection_date']);

                let formattedStartTime = formatTime(item['start_collection_time']);
                let formattedEndTime = formatTime(item['end_collection_time']);

                let row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                        ${item['image'] ? `<img src="/upload/food/small/${item['image']}" width="50" height="50">` : `<img src="/upload/no_image.jpg" width="50" height="50">`}
                        </td>
                        <td>${item['name']}</td>
                        <td>${formattedExpireDate}</td>
                        <td>${formattedCollectionDate}</td>
                        <td>${formattedStartTime} - ${formattedEndTime}</td>
                        <td>${item['user']['firstName']}</td>
                        <td>
                            <span class="badge ${
                                item['status'] === 'pending' ? 'bg-danger' :
                                item['status'] === 'published' ? 'bg-primary' :
                                'bg-success'
                            }">
                            ${item['status']}
                            </span>
                        </td>
                        <td>
                            <a  href="/admin/food/details/${item['id']}" class="btn btn-sm btn-outline-primary"><span class="mdi mdi-eye-circle"></span>
                            </a>

                            <a href="/admin/edit/food/${item['id']}" class="btn btn-sm btn-outline-success"><span class="mdi mdi-pencil-outline"></span>
                            </a>

                            <button data-id="${item['id']}" class="btn deleteBtn btn-sm btn-outline-danger"><span class="mdi mdi-trash-can-outline"></span></button>

                        </td>
                    </tr>`;
                tableList.append(row);
            });
            initializeDataTable();
            attachEventListeners();

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
            "serverSide": false, // You can switch to true if you want server-side processing
            "autoWidth": false,
            "ordering": true,
            "searching": true, // Enable search
            "lengthMenu": [10, 25, 50, 100], // Options for "Show X entries"
            "pageLength": 10, // Default number of rows per page
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
           if (error.response.status === 500) {
                errorToast(error.response.data.error || "An internal server error occurred.");
            } else {
                errorToast("Request failed!");
            }
        } else {
            errorToast("Request failed!");
        }
    }

    function formatDate(dateString) {
        let date = new Date(dateString);
        return date.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });
    }
    
    function formatTime(timeString) {
        let date = new Date('1970-01-01T' + timeString + 'Z');
        let hours = date.getUTCHours();
        let minutes = date.getUTCMinutes();
        let seconds = date.getUTCSeconds();

        let amPm = hours >= 12 ? 'PM' : 'AM';

        hours = hours % 12;
        hours = hours ? hours : 12; 
        minutes = minutes < 10 ? '0' + minutes : minutes;
        seconds = seconds < 10 ? '0' + seconds : seconds;

        return `${hours}:${minutes}:${seconds} ${amPm}`;
    }
</script>


