<template>
    <div class="col-2 col-sm-2">
        <div class="list-group">
            <router-link to="/message/compose" :class="[menu === 'compose' ? 'active' : '', 'list-group-item']" v-tooltip="trans('message.compose')"><span class="hidden-sm-up"><i class="fas fa-paper-plane"></i></span>
                <span class="hidden-xs-down">
                    {{trans('message.compose')}}
                </span>
            </router-link>
            <router-link to="/message/draft" :class="[menu === 'draft' ? 'active' : '', 'list-group-item']" v-tooltip="trans('message.draft')"><span class="hidden-sm-up"><i class="fas fa-edit"></i></span>
                <span class="hidden-xs-down">
                    {{trans('message.draft')}}
                    <span v-if="statistics.draft">
                        ({{statistics.draft}})
                    </span>
                </span>
            </router-link>
            <router-link to="/message/inbox" :class="[menu === 'inbox' ? 'active' : '', 'list-group-item']" v-tooltip="trans('message.inbox')"><span class="hidden-sm-up"><i class="fas fa-inbox"></i></span>
                <span class="hidden-xs-down">
                    {{trans('message.inbox')}}
                    <span v-if="statistics.inbox">
                        ({{statistics.inbox}})
                    </span>
                </span>
            </router-link>
            <router-link to="/message/sent" :class="[menu === 'sent' ? 'active' : '', 'list-group-item']" v-tooltip="trans('message.sent_box')"><span class="hidden-sm-up"><i class="far fa-share-square"></i></span>
                <span class="hidden-xs-down">
                    {{trans('message.sent_box')}}
                    <span v-if="statistics.sent">
                        ({{statistics.sent}})
                    </span>
                </span>
            </router-link>
            <router-link to="/message/important" :class="[menu === 'important' ? 'active' : '', 'list-group-item']" v-tooltip="trans('message.important')"><span class="hidden-sm-up"><i class="fas fa-star"></i></span>
                <span class="hidden-xs-down">
                    {{trans('message.important')}}
                </span>
            </router-link>
            <router-link to="/message/trash" :class="[menu === 'trash' ? 'active' : '', 'list-group-item']" v-tooltip="trans('message.trash')"><span class="hidden-sm-up"><i class="fas fa-trash"></i></span>
                <span class="hidden-xs-down">
                    {{trans('message.trash')}}
                    <span v-if="statistics.trash">
                        ({{statistics.trash}})
                    </span>
                </span>
            </router-link>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            menu: {
                default: ''
            },
            statistics: {

            }
        },
        mounted(){
        }
    }
</script>
