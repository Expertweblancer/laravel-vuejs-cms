<template>
    <div class="dropdown">
      <button type="button" style="margin-top:-5px;" class="btn btn-info btn-sm" href="#" role="button" id="sortByLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-sort-alpha-down"></i> <span class="d-none d-sm-inline">{{trans('general.sort_by')}}</span>
      </button>
      <div :class="['dropdown-menu',getConfig('direction') == 'ltr' ? 'dropdown-menu-right' : '']" aria-labelledby="sortByLink">
        <button style="cursor:pointer;" class="dropdown-item" v-for="option in sortByOptions" @click="$emit('updateOrder',option.value)">
        	{{option.translation}} <span v-if="option.value == order" class="pull-right"><i class="fas fa-check"></i></span> 
        </button>
        <div class="dropdown-divider"></div>
        <button style="cursor:pointer;" class="dropdown-item" v-for="option in orderByOptions" @click="$emit('updateSortBy',option.value)">
        	{{option.translation}} <span v-if="option.value == sortBy" class="pull-right"><i class="fas fa-check"></i></span> 
        </button>
      </div>
    </div>
</template>

<script>
	export default {
		props: {
			sortBy: {
				required: true,
				default: 'created_at'
			},
			order: {
				required: true,
				default: 'desc'
			},
			orderByOptions: {
				required: true,
				default: []
			}
		},
		data() {
			return {
				sortByOptions: [
					{
						value: 'asc',
						translation: i18n.general.ascending
					},
					{
						value: 'desc',
						translation: i18n.general.descending
					}
				]
			}
		},
		methods: {
			getConfig(config){
				return helper.getConfig(config);
			}
		}
	}
</script>