
<div id="result" style="display:none;">
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
</div>



<div class="row" id='search'>
  <!-- Search by Date -->
  <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5>Search by Date</h5><hr>
          <div>
            <label for="defaultFormControlInput" class="form-label">Date: <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="single-date" name="single-date" placeholder="Select a date" aria-describedby="defaultFormControlHelp" />
            <span id="single-date-error" class="text-danger"></span>
          </div>
        </div>
        <div class="card-footer">
          <button class="btn btn-outline-primary" onclick="Save('single')">Search</button>
        </div>
      </div>
  </div>

  <!-- Search by Date Range -->
  <div class="col-md-8">
      <div class="card">
        <div class="card-body">
          <h5>Search by Date Range</h5><hr>
          <div class="row">
            <div class="col-md-6">
              <div>
                <label for="defaultFormControlInput" class="form-label">Start Date: <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="start-date" name="start-date" placeholder="Select start date" aria-describedby="defaultFormControlHelp" />
                <span id="start-date-error" class="text-danger"></span>
              </div>
            </div> 
            <div class="col-md-6">
              <div>
                <label for="defaultFormControlInput" class="form-label">End Date: <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="end-date" name="end-date" placeholder="Select end date" aria-describedby="defaultFormControlHelp" />
                <span id="end-date-error" class="text-danger"></span>
              </div>
            </div>      
          </div>

        </div>


        <div class="card-footer">
          <button class="btn btn-outline-primary" onclick="Save('range')">Search</button>
        </div>
      </div>
  </div>
</div> 

<script>
  async function Save(criteria) {
    let formData = new FormData();

    document.getElementById('single-date-error').innerText = '';
    document.getElementById('start-date-error').innerText = '';
    document.getElementById('end-date-error').innerText = '';

    if (criteria === 'single') {
      let singleDate = document.getElementById('single-date').value;

      if (!singleDate) {
        document.getElementById('single-date-error').innerText = 'Please select a date!';
        return;
      }

      formData.append('date', singleDate);

    } else if (criteria === 'range') {
      let startDate = document.getElementById('start-date').value;
      let endDate = document.getElementById('end-date').value;

      if (!startDate) {
        document.getElementById('start-date-error').innerText = 'Please select a start date!';
        return;
      }

      if (!endDate) {
        document.getElementById('end-date-error').innerText = 'Please select an end date!';
        return;
      }

      formData.append('start_date', startDate);
      formData.append('end_date', endDate);
    }

    const config = {
      headers: {
        'content-type': 'multipart/form-data',
      },
    };

    showLoader();
    try {
      let res = await axios.post("/client/order/by/search", formData, config);

      if (res.status === 200) {
        document.getElementById('result').style.display = 'block';
        document.getElementById('search').style.display = 'none';
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
                        <a  href="/client/order/details/${item['id']}" class="btn btn-sm btn-outline-primary"><span class="mdi mdi-eye-circle"></span>
                        </a>
                    </td>
                </tr>`;
            tableList.append(row);
        });
        initializeDataTable(); 
      }
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
      let errorMessage = 'An unexpected error occurred.';
      if (error.response) {
        const status = error.response.status;
        errorMessage = error.response.data.message || errorMessage;

        if (status === 400) {
          document.getElementById('single-date-error').innerText = error.response.data.message || 'Please provide a valid date';
          document.getElementById('start-date-error').innerText = error.response.data.message || 'Please select a start date!';
          document.getElementById('end-date-error').innerText = error.response.data.message || 'Please select an end date!';
        } else if (status === 500) {
          errorMessage = error.response.data.message || 'An unexpected error occurred.';
        } else {
          errorToast(errorMessage);
        }
      }
    }
</script>

