<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
    const userId = "{{ $_SESSION['user_id'] ?? 0 }}";
    let savedFlight = localStorage.getItem('selected_flight_' + userId);

    if (!savedFlight && userId > 0) {
        const guestFlight = localStorage.getItem('selected_flight_0');
        if (guestFlight) {
            localStorage.setItem('selected_flight_' + userId, guestFlight);
            localStorage.removeItem('selected_flight_0');
            const guestBooking = localStorage.getItem('booking_data_0');
            if (guestBooking) {
                localStorage.setItem('booking_data_' + userId, guestBooking);
                localStorage.removeItem('booking_data_0');
            }
            savedFlight = guestFlight;
        }
    }

    if (savedFlight) {
        const flight = JSON.parse(savedFlight);
        if (flight && flight.id) {
            window.location.href = "{{ route('checkout') }}?flight_id=" + flight.id;
        } else {
            showNoFlightAlert();
        }
    } else {
        showNoFlightAlert();
    }

    function showNoFlightAlert() {
        Swal.fire({
            icon: 'info',
            title: 'Chưa có thông tin đặt vé',
            text: 'Bạn chưa chọn chuyến bay nào. Vui lòng chọn chuyến bay trước khi thanh toán!',
            confirmButtonText: 'Quay lại trang đặt vé',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('cheap-tickets') }}";
            }
        });
    }
</script>
</body>
</html>
