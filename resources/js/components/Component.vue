<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Розыгрыш призов</div>
                    <div class="card-body">
                        В розограше {{prizes.money}}рублей<br>
                        Есть возможность получить следующие призы:
                        <div v-for="prize in prizes.prizes">{{ prize.name }} (осталось {{prize.count}}шт)</div>
                    </div>

                    <p style="color: rebeccapurple" v-if="msg">{{msg}}</p>
                    <button v-if="!sending" class="btn btn-primary" @click="getPrize">Получить приз!</button>

                    <button v-if="prize === 'money'" class="btn btn-primary" @click="acceptPrize('Начислено')">
                        Принять
                    </button>

                    <button v-if="prize === 'money'" class="btn btn-primary" @click="acceptPrize('Переведено в балы')">
                        В балы
                    </button>


                    <button v-if="prize === 'prize'" class="btn btn-primary" @click="acceptPrize('Ожидает отправки')">
                        Отправляем почтой
                    </button>

                    <button v-if="prize === 'prize'" class="btn btn-primary" @click="acceptPrize('Отказ от приза')">
                        Конвертим в баллы
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    export default {
        props: ['prizes'],
        data() {
            return {
                resultId: 0,
                sending: false,
                msg: '',
                status: '',
                prize: '',
            }
        },
        methods: {
            acceptPrize: function(status) {
                let formData = new FormData();
                formData.append('resultId', this.resultId);
                formData.append('status', status);
                axios({
                    method: 'post',
                    url: '/api/rafles/acceptPrize',
                    data: formData,
                }).then(response => {
                    this.msg = '';
                    if (!response || !response.data || response.statusText !== 'OK' || !response.data.status) {
                        this.msg = 'Что-то пошло не так(';
                        this.sending = false;
                        console.log(response);
                        return;

                    }

                    if (response.data.status === 'error') {
                        this.msg = 'Что-то пошло не так(';
                        return;
                    }
                    if (response.data.msg) {
                        this.msg = response.data.msg;
                    }
                    this.resultId = 0;
                    this.prize = '';
                    this.status = '';
                    this.sending = false;
                });
            },
            getPrize: function () {
                this.sending = true;
                this.msg = 'Ожидаем ответ от сервера';

                axios.post('/api/rafles/getPrize').then(response => {
                    this.msg = '';
                    if (!response || !response.data || response.statusText !== 'OK' || !response.data.resultId) {
                        this.msg = 'Что-то пошло не так(';
                        this.sending = false;
                        console.log(response);
                        return;
                    }

                    this.msg = 'Ждем результата';

                    this.resultId = response.data.resultId;
                    setTimeout(()=>{
                        this.getResult();
                    },2000)

                });
            },

            getResult: function () {
                let formData = new FormData();
                formData.append('resultId', this.resultId);
                axios({
                    method: 'post',
                    url: '/api/rafles/getResult',
                    data: formData,
                }).then(response => {
                    this.msg = '';
                    if (!response || !response.data || response.statusText !== 'OK' || !response.data.status) {
                        this.msg = 'Что-то пошло не так(';
                        this.sending = false;
                        console.log(response);
                        return;
                    }
                    console.log(response);
                    this.msg = 'Ждем результата';

                    if (response.data.status) {
                        this.status = response.data.status;
                    }

                    if (this.status === 'В очереди') {
                        setTimeout((resultId)=>{
                            this.getResult(resultId);
                        },1000);
                        return;
                    }

                    this.msg = this.status;

                    if (this.status === 'Переведено в балы') {
                        this.sending = false;
                    }

                    if (!response.data.prizes) {
                        return;
                    }

                    if (response.data.prizes.balls) {
                        this.msg += ` Получено ${response.data.prizes.balls} баллов`;
                        this.prize = 'balls';
                    }
                    if (response.data.prizes.money) {
                        this.msg += ` Получено ${response.data.prizes.money} рублей`;
                        this.prize = 'money';
                    }
                    if (response.data.prizes.prize) {
                        this.msg += ` Получен ${response.data.prizes.prize} приз`;
                        this.prize = 'prize';
                    }
                });
            }
        },
    }
</script>
