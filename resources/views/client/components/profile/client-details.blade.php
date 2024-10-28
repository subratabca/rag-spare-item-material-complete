<div class="row">
  <div class="col-xl-6 col-lg-6 col-md-6 order-1 order-md-0">
    <div class="card mb-4">
      <div class="card-body">
        <div class="user-avatar-section">
          <div class="d-flex align-items-center flex-column">
            <img
              class="img-fluid rounded mb-3 mt-4"
              src=""
              id="client-image"
              height="120"
              width="120"
              alt="User avatar" />
            <div class="user-info text-center">
              <h4><span id="client-name"> </span></h4>
              <span class="badge bg-label-danger rounded-pill">Client</span>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-between flex-wrap my-2 py-3">
          <div class="d-flex align-items-center me-4 mt-3 gap-3">
            <div class="avatar">
              <div class="avatar-initial bg-label-primary rounded">
                <i class="mdi mdi-check mdi-24px"></i>
              </div>
            </div>
            <div>
              <h4 class="mb-0" id="orders-count"> </h4>
              <span>Total Orders</span>
            </div>
          </div>
          <div class="d-flex align-items-center mt-3 gap-3">
            <div class="avatar">
              <div class="avatar-initial bg-label-primary rounded">
                <i class="mdi mdi-food-outline mdi-24px"></i>
              </div>
            </div>
            <div>
              <h4 class="mb-0" id="foods-count"></h4>
              <span>Total Uploaded Foods</span>
            </div>
          </div>
          <div class="d-flex align-items-center mt-3 gap-3">
            <div class="avatar">
              <div class="avatar-initial bg-label-primary rounded">
                <i class="mdi mdi-account-group-outline mdi-24px"></i>
              </div>
            </div>
            <div>
              <h4 class="mb-0" id="customers-count"></h4>
              <span>Total Customers</span>
            </div>
          </div>
        </div>
        <h5 class="pb-3 border-bottom mb-3">Details</h5>
        <div class="info-container">
          <ul class="list-unstyled mb-4">
            <li class="mb-3">
              <span class="fw-medium text-heading me-2">Name:</span>
              <span id="client-name2"></span>
            </li>
            <li class="mb-3">
              <span class="fw-medium text-heading me-2">Email:</span>
              <span id="client-email"> </span>
            </li>
            <li class="mb-3">
              <span class="fw-medium text-heading me-2">Contact:</span>
              <span id="client-phone"> </span>
            </li>
            <li class="mb-3">
              <span class="fw-medium text-heading me-2">Registration Date:</span>
              <span id="client-registration-date"> </span>
            </li>
            <li class="mb-3">
              <span class="fw-medium text-heading me-2">Registration Time:</span>
              <span id="client-registration-time"> </span>
            </li>
            <li class="mb-3">
              <span class="fw-medium text-heading me-2">Account Status:</span>
              <span id="client-account-status"> </span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>


<script>
  document.addEventListener("DOMContentLoaded", function () {
    ClientDetailsInfo();
  });

  async function ClientDetailsInfo() {
    showLoader();
    try {
      let url = window.location.pathname;
      let segments = url.split('/');
      let id = segments[segments.length - 1];

      let res = await axios.get("/client/account/details/info/" + id);

      let imageUrl = res.data.data['image']
        ? `/upload/client-profile/small/${res.data.data['image']}`
        : `/upload/no_image.jpg`;

      let firstName = res.data.data['firstName'];
      let lastName = res.data.data['lastName'];
      let fullName = lastName ? `${firstName} ${lastName}` : firstName;

      let mobileNumber = res.data.data['mobile'];
      let phoneBadge = mobileNumber
        ? `<span class="badge bg-success">${mobileNumber}</span>`
        : `<span class="badge bg-info">Contact Number Not Found</span>`;

        let createdAt = new Date(res.data.data['created_at']);

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

      let isEmailVerified = res.data.data['is_email_verified'];
      let accountStatus = isEmailVerified === 1
        ? `<span class="badge bg-success">Active</span>`
        : `<span class="badge bg-danger">Inactive</span>`;

      document.getElementById('client-image').src = imageUrl;
      document.getElementById('client-name').innerText = fullName;
      document.getElementById('orders-count').innerText = res.data.data['total_orders'];
      document.getElementById('foods-count').innerText = res.data.data['foods_count'];
      document.getElementById('customers-count').innerText = res.data.data['total_customers'];
      document.getElementById('client-name2').innerText = fullName;
      document.getElementById('client-email').innerText = res.data.data['email'];
      document.getElementById('client-phone').innerHTML = phoneBadge;
      document.getElementById('client-registration-date').innerText = registrationDate;
      document.getElementById('client-registration-time').innerText = registrationTime;
      document.getElementById('client-account-status').innerHTML = accountStatus;

    } catch (error) {
        if (error.response) {
            const { status, data } = error.response;
            const message = data.message || 'An unexpected error occurred';

            if (status === 404 && data.status === 'failed') {
                errorToast(data.message || 'User not found');
            } else if (status === 500) {
                errorToast('Server error: ' + message);
            } else {
                errorToast(message); 
            }
        } else {
            errorToast('Error: ' + error.message); 
        }
    } finally {
      hideLoader();      
    }
  }

</script>
