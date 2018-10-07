<template>
	<div class="module-info">
		<p v-if="icon" class="text-muted"><i :class="['fa','fa-'+icon,'fa-3x']"></i></p>
        <h2 v-if="title" v-text="trans(module+'.'+title)"></h2>
        <p v-if="description" class="text-themecolor" v-text="trans(module+'.'+description)"></p>
        <slot name="btn"></slot>
	</div>
</template>

<script>
	export default {
		props: ['module','icon','title','description']
	}
</script>