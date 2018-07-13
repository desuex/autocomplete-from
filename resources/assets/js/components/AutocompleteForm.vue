<template>
    <div class="container">
        <div class="row justify-content-center">
            <div>
                <div class="card card-default search-box">
                    <div class="card-header">Geo search with autocomplete</div>

                    <div class="card-body">


                        <!-- Search form -->
                        <form class="form-inline ">
                            <input class="form-control form-control-sm mr-3 search-form" type="text"
                                   @input="onChange"  v-model="search" @keydown.down="onArrowDown"
                                   @keydown.up="onArrowUp" @keydown.enter="onEnter"
                                   placeholder="Search" aria-label="Search">
                            <a href="#"><i class="fa fa-search" aria-hidden="true"></i></a>
                        </form>
                        <div class="search-results">
                            <div class="autocomplete">

                                <ul id="autocomplete-results" v-show="isOpen" class="autocomplete-results">
                                    <li class="loading" v-if="isLoading">
                                        Loading results...
                                    </li>
                                    <li v-else v-for="(result, i) in results" :key="i" @click="setResult(result)" class="autocomplete-result" :class="{ 'is-active': i === arrowCounter }">
                                        {{ result }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'autocomplete-form',

        props: {
            items: {
                type: Array,
                required: false,
                default: () => [],
            },
            isAsync: {
                type: Boolean,
                required: false,
                default: false,
            },
        },

        data() {
            return {
                isOpen: false,
                results: [],
                search: '',
                isLoading: false,
                arrowCounter: 0,
            };
        },

        methods: {
            onChange() {
                this.$emit('input', this.search);
                if(this.search.length<3){
                    this.isOpen = false;
                    return
                }

                if (this.isAsync) {
                    this.isLoading = true;
                } else {
                    this.filterResults();
                    this.isOpen = true;
                }
            },

            filterResults() {
                let self = this;
                fetch('/api/autocomplete?q='+this.search)
                    .then((response) => {
                        if(response.ok) {
                            return response.json();
                        }

                        throw new Error('Network response was not ok');
                    })
                    .then((json) => {
                        self.results = json;
                    })
                    .catch((error) => {
                        console.log(error);
                    });

            },
            setResult(result) {
                this.search = result;
                this.isOpen = false;
            },
            onArrowDown(evt) {
                if (this.arrowCounter < this.results.length) {
                    this.arrowCounter = this.arrowCounter + 1;
                }
            },
            onArrowUp() {
                if (this.arrowCounter > 0) {
                    this.arrowCounter = this.arrowCounter -1;
                }
            },
            onEnter() {
                this.search = this.results[this.arrowCounter];
                this.isOpen = false;
                this.arrowCounter = -1;
            },
            handleClickOutside(evt) {
                if (!this.$el.contains(evt.target)) {
                    this.isOpen = false;
                    this.arrowCounter = -1;
                }
            }
        },
        watch: {
            items: function (val, oldValue) {
                if (val.length !== oldValue.length) {
                    this.results = val;
                    this.isLoading = false;
                }
            },
        },
        mounted() {
            document.addEventListener('click', this.handleClickOutside)
        },
        destroyed() {
            document.removeEventListener('click', this.handleClickOutside)
        }
    };
</script>

<style>
    .search-box {
        width: 500px;
    }
    .search-form{
        width: 420px;
        font-family: Verdana,sans-serif;
    }
    .autocomplete {
        position: relative;
    }

    .autocomplete-results {
        padding: 0;
        margin: 0;
        border: 1px solid #eeeeee;
        height: 120px;
        overflow: auto;
        width: 420px;
        position: absolute;
        background: #fff;
    }

    .autocomplete-result {
        list-style: none;
        text-align: left;
        padding: 4px 2px;
        cursor: pointer;
    }

    .autocomplete-result.is-active,
    .autocomplete-result:hover {
        background-color: #4AAE9B;
        color: white;
    }

</style>
