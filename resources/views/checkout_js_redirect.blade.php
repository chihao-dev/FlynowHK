<script>
    const savedFlight = localStorage.getItem('selected_flight');
    if (savedFlight) {
        const flight = JSON.parse(savedFlight);
        window.location.href = "{{ route('checkout') }}?flight_id=" + flight.id;
    } else {
        alert('Chưa chọn chuyến bay để đặt!');
        window.location.href = "{{ route('cheap-tickets') }}";
    }
</script>
