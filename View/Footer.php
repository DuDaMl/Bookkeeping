<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="/assets/js/bootstrap.min.js"></script>
<script src="/assets/js/bootstrap-datepicker.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>

<script type="text/javascript">
    $(function () {
        $('#datetimepicker1').datepicker({
            format: "yyyy-mm-dd",
        });

        // ��������� ���
       //Default: 'days'
        $('#datetimepicker_day').datepicker({
            format: "yyyy-mm-dd",
            viewMode: 'days',
        });

        // ��������� ������
        //months
        $('#datetimepicker_months').datepicker({
            format: "yyyy-mm",
            viewMode: "months",
            minViewMode: "months"
        });

        // ��������� ����
        // viewMode: 'years'
        $('#datetimepicker_years').datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years"
        });
    });
</script>