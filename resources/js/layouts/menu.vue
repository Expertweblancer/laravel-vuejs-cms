<template>
	<ul id="sidebarnav">
	    <li><router-link to="/home" exact><i class="fas fa-home fa-fw"></i> <span class="hide-menu">{{trans('general.home')}}</span></router-link></li>
		<template v-if="$route.meta.menu == 'configuration'">
	        <li><router-link to="/configuration/basic" exact><i class="fas fa-cog fa-fw"></i> <span class="hide-menu">{{trans('configuration.basic_configuration')}}</span></router-link></li>
	        <li><router-link to="/configuration/logo" exact><i class="fas fa-image fa-fw"></i> <span class="hide-menu">{{trans('general.logo')}}</span></router-link></li>
	        <li><router-link to="/configuration/system" exact><i class="fas fa-cogs fa-fw"></i> <span class="hide-menu">{{trans('configuration.system_configuration')}}</span></router-link></li>
	        <li><router-link to="/configuration/mail" exact><i class="fas fa-envelope fa-fw"></i> <span class="hide-menu">{{trans('mail.mail_configuration')}}</span></router-link></li>
	        <li v-if="getConfig('multilingual')"><router-link to="/configuration/locale" exact><i class="fas fa-globe fa-fw"></i> <span class="hide-menu">{{trans('locale.locale')}}</span></router-link></li>
	        <li><router-link to="/configuration/role" exact><i class="fas fa-users fa-fw"></i> <span class="hide-menu">{{trans('role.role')}}</span></router-link></li>
	        <li><router-link to="/configuration/permission" exact><i class="fas fa-key fa-fw"></i> <span class="hide-menu">{{trans('permission.permission')}}</span></router-link></li>
	        <li><router-link to="/configuration/menu" exact><i class="fas fa-list fa-fw"></i> <span class="hide-menu">{{trans('general.menu')}}</span></router-link></li>
	        <li><router-link to="/configuration/sms" exact><i class="fas fa-comment fa-fw"></i> <span class="hide-menu">{{trans('general.sms')}}</span></router-link></li>
	        <li><router-link to="/configuration/authentication" exact><i class="fas fa-sign-in-alt fa-fw"></i> <span class="hide-menu">{{trans('auth.authentication')}}</span></router-link></li>
	        <li v-if="getConfig('ip_filter')"><router-link to="/configuration/ip-filter" exact><i class="fas fa-ellipsis-v fa-fw"></i> <span class="hide-menu">{{trans('ip_filter.ip_filter')}}</span></router-link></li>
	        <li><router-link to="/configuration/scheduled-job" exact><i class="fas fa-clock fa-fw"></i> <span class="hide-menu">{{trans('general.scheduled_job')}}</span></router-link></li>
		</template>
		<template v-else>
	        <li v-if="hasPermission('list-user') && getConfig('show_user_menu')"><router-link to="/user" exact><i class="fas fa-users fa-fw"></i> <span class="hide-menu">{{trans('user.user')}}</span></router-link></li>
	        <li v-if="hasPermission('access-todo') && getConfig('show_todo_menu')"><router-link to="/todo" exact><i class="far fa-check-circle fa-fw"></i> <span class="hide-menu">{{trans('todo.todo')}}</span></router-link></li>
	        <li v-if="hasPermission('access-todo') && getConfig('show_todo_menu')"><router-link to="/proefritten" exact><i class="far fa-check-circle fa-fw"></i> <span class="hide-menu">{{trans('todo.proefritten')}}</span></router-link></li>
	        <li v-if="hasPermission('access-todo') && getConfig('show_todo_menu')"><router-link to="/lease-aanvragen" exact><i class="far fa-check-circle fa-fw"></i> <span class="hide-menu">{{trans('todo.Lease aanvragen')}}</span></router-link></li>
	        <li v-if="hasPermission('access-message') && getConfig('show_message_menu')"><router-link to="/message" exact><i class="far fa-envelope-open fa-fw"></i> <span class="hide-menu">{{trans('message.message')}}</span></router-link></li>
	        <li v-if="hasPermission('access-configuration') && getConfig('show_configuration_menu')"><router-link to="/configuration" exact><i class="fas fa-cogs fa-fw"></i> <span class="hide-menu">{{trans('configuration.configuration')}}</span></router-link></li>
	        <li v-if="hasPermission('access-configuration') && getConfig('show_backup_menu')"><router-link to="/backup" exact><i class="fas fa-database fa-fw"></i> <span class="hide-menu">{{trans('backup.backup')}}</span></router-link></li>
	        <li v-if="hasPermission('access-configuration') && getConfig('show_email_template_menu')"><router-link to="/email-template" exact><i class="far fa-envelope fa-fw"></i> <span class="hide-menu">{{trans('template.email_template')}}</span></router-link></li>
	        <li v-if="hasPermission('access-configuration') && getConfig('show_email_log_menu')"><router-link to="/email-log" exact><i class="fas fa-envelope fa-fw"></i> <span class="hide-menu">{{trans('mail.email_log')}}</span></router-link></li>
	        <li v-if="hasPermission('access-configuration') && getConfig('show_activity_log_menu')"><router-link to="/activity-log" exact><i class="fas fa-bars fa-fw"></i> <span class="hide-menu">{{trans('activity.activity_log')}}</span></router-link></li>
	    </template>
    </ul>
</template>

<script>
	export default {
		methods: {
			hasPermission(permission){
				return helper.hasPermission(permission);
			},
			hasRole(role){
				return helper.hasRole(role);
			},
			getConfig(config){
				return helper.getConfig(config);
			}
		}
	}
</script>
