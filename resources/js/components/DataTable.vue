<template>
    <div class="data-table">
        <table class="data-table__inner">
            <thead class="data-table__thead">
            <tr class="data-table__head-row">
                <th v-if="hasCheckbox" class="data-table__th">
                    <field-checkbox
                    @input="selectAll"
                    :value="isAllSelected"
                    :data="[1]"
                    :has-label="false"
                    name="data-table-checkbox"/>
                </th>
                <th class="data-table__th" v-if="hasRadio">#</th>
                <template v-for="(cell, index) in header">
                    <slot name="th" :data="{cell}">
                        <th
                        :key="index"
                        class="data-table__th">{{cell.label}}</th>
                    </slot>
                </template>

            </tr>
            </thead>

            <tbody>
            <template v-for="(row, index) in body">
                <slot name="row" :row="row">
                    <tr
                    @click="$emit('table:select-row', row)"
                    :key="index"
                    class="data-table__body-row">

                        <td
                        v-if="hasCheckbox"
                        class="data-table__td">
                            <field-checkbox
                            v-model="model"
                            @click.native.stop
                            :data="[row]"
                            :has-label="false"
                            name="data-table-checkbox"/>
                        </td>

                        <td
                        v-if="hasRadio"
                        class="data-table__td">
                            <field-radio
                            v-model="model"
                            @click.stop
                            @click.native.stop
                            :data="[row]"
                            :has-label="false"
                            name="data-table-radio"/>
                        </td>

                        <template v-for="(cell, key) in withHeaderNames(row)">
                            <slot name="td" :data="{cell, key, row}">
                                <td class="data-table__td" :key="key" >{{cell}}</td>
                            </slot>
                        </template>
                    </tr>
                </slot>
            </template>
            </tbody>
        </table>

        <loader
        :is-loading="isLoading"/>
    </div>
</template>
<script>
import FieldCheckbox from './Fields/FieldCheckbox';
import FieldRadio from './Fields/FieldRadio';
import Loader from './Loader';

export default {
    name: 'DataTable',
    components: {Loader, FieldCheckbox, FieldRadio },
    props: {
        header: {type: Array, required: true },
        body: {type: Array, required: true},
        hasCheckbox: { type: Boolean, default: false },
        hasRadio: { type: Boolean, default: false },
        isLoading: { type: Boolean, default: false },
        value: null
    },

    data() {
        return{
            isAllSelected: false,
        }
    },

    computed: {
        model: {
            get() {
                return this.value
            },

            set(value) {
                this.isAllSelected = value.length === this.body.length && this.body.length != 0;
                this.$emit('input', value);
            }
        }
    },

    methods: {
        withHeaderNames(row) {
            const fields = this.header.map(h => h.field);
            return fields.reduce((result, field) => {
                result[field] = row[field];
                return result;
            }, {});
        },

        selectAll(value){
            this.isAllSelected = value;
            if (value){
                this.$emit('input', this.body);
            }
            else{
                this.$emit('input', []);
            }
        }
    }
}
</script>
<style lang="scss">

.data-table {

    position: relative;

    &__inner {
        height: 100%;
        min-width: 100%;
        border-collapse: collapse;
    }

    &__th {
        padding: 20px;
        border-bottom: 2px solid #d3d8d8;
        color: #7b898d;
        text-align: left;
        font-weight: 400;
    }

    &__body-row {
        cursor: pointer;

        &:hover {
            background-color: #ededed;
        }
    }

    &__td {
        padding: 20px;
        border-bottom: 1px solid #d3d8d8;

        max-width: 180px;
        vertical-align: middle;
        overflow-wrap: break-word;
    }
}
</style>