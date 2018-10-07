<template>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label for="">{{label || trans('general.date_range')}}</label>
                <div class="input-group">
                    <datepicker :bootstrapStyling="true" input-class="form-control" :value="startDate" :placeholder="trans('general.start_date')" :clear-button="clearButton" @selected="updateStartDate" @cleared="clearStartDate"></datepicker>
                    <datepicker :bootstrapStyling="true" input-class="form-control m-l-10" :value="endDate" :placeholder="trans('general.end_date')" @selected="updateEndDate" @cleared="clearEndDate"></datepicker>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import datepicker from 'vuejs-datepicker'

    export default {
        components: {datepicker},
        props: ['startDate','endDate','label'],
        data(){
            return {
                clearButton: true
            }
        },
        methods: {
            updateStartDate(val){
                let date = moment(val).format('YYYY-MM-DD');
                if(date > this.endDate)
                    this.$emit('update:endDate',date);
                this.$emit('update:startDate',date);
            },
            updateEndDate(val){
                let date = moment(val).format('YYYY-MM-DD');
                if(!this.startDate || this.startDate > date)
                    this.$emit('update:startDate',date);
                this.$emit('update:endDate',date);
            },
            clearStartDate(){
                this.$emit('update:startDate','');
                this.$emit('update:endDate','');
            },
            clearEndDate(){
                this.$emit('update:endDate','');
            }
        }
    }
</script>
