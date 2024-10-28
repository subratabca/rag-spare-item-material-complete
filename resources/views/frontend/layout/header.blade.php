    <nav class="layout-navbar container shadow-none py-0">
      <div class="navbar navbar-expand-lg landing-navbar border-top-0 px-3 px-md-4">
        <!-- Menu logo wrapper: Start -->
        <div class="navbar-brand app-brand demo d-flex py-0 py-lg-2 me-4">
          <!-- Mobile menu toggle: Start-->
          <button
            class="navbar-toggler border-0 px-0 me-2"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent"
            aria-expanded="false"
            aria-label="Toggle navigation">
            <i class="tf-icons mdi mdi-menu mdi-24px align-middle"></i>
          </button>
          <!-- Mobile menu toggle: End-->
          <a href="{{ route('home') }}" class="app-brand-link">
          <span class="app-brand-logo demo">
              <img id="logo" src="/upload/no_image.jpg" width="100" height="40" alt="App Logo">
          </span>

          </a>
        </div>
        <!-- Menu logo wrapper: End -->
        <!-- Menu wrapper: Start -->
        <div class="collapse navbar-collapse landing-nav-menu" id="navbarSupportedContent">
          <button
            class="navbar-toggler border-0 text-heading position-absolute end-0 top-0 scaleX-n1-rtl"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent"
            aria-expanded="false"
            aria-label="Toggle navigation">
            <i class="tf-icons mdi mdi-close"></i>
          </button>
          <ul class="navbar-nav me-auto p-3 p-lg-0">
            <li class="nav-item">
              <a class="nav-link fw-medium" aria-current="page" href="{{ route('home') }}">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-medium" href="{{ route('about') }}">About</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-medium text-nowrap" href="{{ route('contact.us.page') }}">Contact us</a>
            </li>
            <li>
              <a href="{{ route('client.registration.page') }}" class="btn btn-info px-2 px-sm-4 px-lg-2 px-xl-4">
                <span class="tf-icons mdi mdi-account me-md-1"></span><span class="d-none d-md-block">Client Registration</span>
              </a
              >
            </li>
          </ul>
        </div>
        <div class="landing-menu-overlay d-lg-none"></div>
        <!-- Menu wrapper: End -->
        <!-- Toolbar: Start -->
        <ul class="navbar-nav flex-row align-items-center ms-auto">

          <!-- navbar button: Start -->
          @if(Cookie::get('token') !== null)
          <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-2 me-xl-1">
            <a
              class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
              href="javascript:void(0);"
              data-bs-toggle="dropdown"
              data-bs-auto-close="outside"
              aria-expanded="false">
              <i class="mdi mdi-bell-outline mdi-24px"></i>
              <span
                class="position-absolute top-0 start-50 translate-middle-y badge badge-dot bg-danger mt-2 border" id="notificationCount"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end py-0">
              <li class="dropdown-menu-header border-bottom">
                <div class="dropdown-header d-flex align-items-center py-3">
                  <h6 class="mb-0 me-auto">Notification<span id="notificationCount1" class="badge rounded-pill bg-label-primary">0 New</span></h6>
                  <a href="javascript:void(0);" onclick="markAllAsRead()" style="color:green">Mark All As Read</a>
                </div>
              </li>
              <li class="dropdown-notifications-list scrollable-container">
                <ul class="list-group list-group-flush">
                  <!-- Notifications will be populated here dynamically -->
                </ul>
              </li>
              <li class="dropdown-menu-footer border-top p-2 mt-4">
                <a href="{{ route('notifications') }}" class="btn btn-primary d-flex justify-content-center">
                  View all notifications
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-2 me-xl-1">
            <a class="nav-link btn btn-text-secondary  dropdown-toggle hide-arrow"
              href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" 
              aria-expanded="false" id="login-user-name">Account
            </a>
            <ul class="dropdown-menu dropdown-menu-end py-0">
              <li class="dropdown-menu-header border-bottom">
                <a href="{{ route('user.dashboard') }}"><div class="dropdown-header d-flex align-items-center py-3">
                  <h6 class="mb-0 me-auto">Dashboard</h6>
                </div></a>
              </li>
              <li class="dropdown-menu-header border-bottom">
                <a href="{{ route('logout') }}"><div class="dropdown-header d-flex align-items-center py-3">
                  <h6 class="mb-0 me-auto">Logout</h6>
                </div></a>
              </li>
            </ul>
          </li>
          @else
          <li>
            <a href="{{ route('login.page') }}" class="btn btn-primary px-2 px-sm-4 px-lg-2 px-xl-4">
              <span class="tf-icons mdi mdi-account me-md-1"></span><span class="d-none d-md-block">Login/Register</span>
            </a
            >
          </li>
          @endif 
          <!-- navbar button: End -->
        </ul>
        <!-- Toolbar: End -->
      </div>
    </nav>

@if(Cookie::get('token') !== null)
<script>
document.addEventListener("DOMContentLoaded", async function () {
  try {
      const response = await axios.get('/user/limited/notification/list');

      if (response.status === 200) {
          const userData = response.data.data;
          const unreadNotifications = response.data.unreadNotifications;
          const readNotifications = response.data.readNotifications;

          const notificationCount = unreadNotifications.length || '0';
          document.getElementById('notificationCount').innerText = notificationCount;
          document.getElementById('notificationCount1').innerText = notificationCount;
          displayNotifications(unreadNotifications, readNotifications);
          
          document.getElementById('login-user-name').innerText = userData.firstName || 'Account';
          document.getElementById('firstName').innerText = userData.firstName || 'No User';
          document.getElementById('mobile').innerText = userData.mobile || 'No Number';
          document.getElementById('email').innerText = userData.email;
          document.getElementById('commonImg').src = userData['image'] ? "/upload/user-profile/small/" + userData['image'] : "/upload/no_image.jpg";
      }
  } catch (error) {
      if (error.response) {
          const status = error.response.status;
          const message = error.response.data.message || 'An unexpected error occurred';

          if (status === 400) {
              errorToast(message || 'Bad Request');
          } else if (status === 500) {
              errorToast(message || 'Server Error');
          } else {
              errorToast(message);
          }
      } else {
          errorToast1('No response received from the server.');
      }
  }

function displayNotifications(unreadNotifications, readNotifications) {
    const notificationsContainer = document.querySelector('.dropdown-notifications-list ul');
    let notificationsHTML = '';

    if ((unreadNotifications && unreadNotifications.length === 0) &&
        (readNotifications && readNotifications.length === 0)) {
        notificationsContainer.innerHTML = '<li class="list-group-item">No notifications</li>';
        return;
    }


    function getNotificationLink(notification) {
        if (notification.data.order_id) {
            return `/user/order-details/${notification.data.order_id}?notification_id=${notification.id}`;
        } else if (notification.data.complain_id) {
            return `/user/complain-details/${notification.data.complain_id}?notification_id=${notification.id}`;
        } else {
            return '#';
        }
    }
    


    if (unreadNotifications && unreadNotifications.length > 0) {
        unreadNotifications.forEach(notification => {
            const link = getNotificationLink(notification); 
            notificationsHTML += `
                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                    <div class="d-flex gap-2">
                        <a href="${link}"><div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                            <h6 class="mb-1 text-truncate"><strong>${notification.data.data}</strong></h6>
                            <small class="text-truncate text-body">${new Date(notification.created_at).toLocaleString()}</small>
                        </div></a>
                        <div class="flex-shrink-0 dropdown-notifications-actions">
                            <small class="text-muted">Unread</small>
                        </div>
                    </div>
                    <button class="delete-notification-btn btn btn-danger btn-sm mt-2" onclick="deleteNotification('${notification.id}')">Delete</button>
                </li>`;
        });
    }

    if (readNotifications && readNotifications.length > 0) {
        readNotifications.forEach(notification => {
            const link = getNotificationLink(notification); 
            notificationsHTML += `
                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                    <div class="d-flex gap-2">
                        <a href="${link}"><div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                            <h6 class="mb-1 text-truncate">${notification.data.data}</h6>
                            <small class="text-truncate text-body">${new Date(notification.created_at).toLocaleString()}</small>
                        </div></a>
                        <div class="flex-shrink-0 dropdown-notifications-actions">
                            <small class="text-muted">Read</small>
                        </div>
                    </div>
                    <button class="delete-notification-btn btn btn-danger btn-sm mt-2" onclick="deleteNotification('${notification.id}')">Delete</button>
                </li>`;
        });
    }

    notificationsContainer.innerHTML = notificationsHTML;
}


});


async function deleteNotification(notificationId) {
    try {
        const response = await axios.delete(`/user/delete/notification/${notificationId}`);

        if (response.status === 200) {
            successToast(response.data.message || 'Request success');
            window.location.reload();
        } else {
            errorToast(response.data.message || 'Failed to delete notification');
        }
    } catch (error) {
        if (error.response) {
            const status = error.response.status;
            const message = error.response.data.message || 'An unexpected error occurred';

            if (status === 404) {
                if (error.response.data.status === 'failed to fetch user') {
                    errorToast(error.response.data.message || 'User not found');
                } else if (error.response.data.status === 'failed') {
                    errorToast(error.response.data.message || 'Notification not found');
                } else {
                    errorToast(message); // Catch-all for other 404 cases
                }
            } else if (status === 500) {
                errorToast('Server error: ' + message);
            } else {
                errorToast(message); // Catch-all for other status codes
            }
        } else {
            errorToast('Error: ' + error.message); // For errors not from the server
        }
    }

}


async function markAllAsRead() {
      try {
          const response = await axios.get('/user/markAsRead');

          if (response.status === 200 && response.data.status === 'success') {
              document.getElementById('notificationCount').innerText = response.data.unreadCount === 0 ? '0 New' : `${response.data.unreadCount} New`;

              const notificationItems = document.querySelectorAll('.dropdown-notifications-actions small');
              notificationItems.forEach(item => {
                  item.innerText = 'Read';
                  item.classList.remove('text-muted');
                  item.classList.add('text-success');
              });

              successToast(response.data.message || 'Notifications marked as read');
              window.location.reload();
          }
      } catch (error) {
          if (error.response) {
              const status = error.response.status;
              const message = error.response.data.message || 'An unexpected error occurred';

              if (status === 400) {
                  errorToast(message || 'Bad Request');
              } else if (status === 404) {
                  errorToast(message || 'Not Found');
              } else if (status === 500) {
                  errorToast(message || 'Server Error');
              } else {
                  errorToast(message);
              }
          } else {
              errorToast('No response received from the server.');
          }
      }
}


</script>
@endif
