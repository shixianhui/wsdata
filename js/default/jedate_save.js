$("#create_time_start").jeDate({
    onClose:false,
    isTime:false,
    format: "YYYY-MM-DD"
});
$("#create_time_end").jeDate({
    onClose:false,
    isTime:false,
    format: "YYYY-MM-DD"
});
$('#add_time').jeDate({
    onClose:false,
    festival: false,
    format: "YYYY-MM-DD hh:mm:ss",
    trigger: "click focus"
});