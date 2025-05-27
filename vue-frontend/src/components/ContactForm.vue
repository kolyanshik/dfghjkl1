<template>
  <form @submit.prevent="submitForm">
    <div>
      <label for="name">Имя:</label>
      <input id="name" v-model="form.name" type="text" />
      <div v-if="errors.name" style="color: red;">{{ errors.name[0] }}</div>
    </div>

    <div>
      <label for="email">Email:</label>
      <input id="email" v-model="form.email" type="email" />
      <div v-if="errors.email" style="color: red;">{{ errors.email[0] }}</div>
    </div>

    <div>
      <label for="message">Сообщение:</label>
      <textarea id="message" v-model="form.message"></textarea>
      <div v-if="errors.message" style="color: red;">{{ errors.message[0] }}</div>
    </div>

    <button type="submit" :disabled="loading">
      {{ loading ? "Отправка..." : "Отправить" }}
    </button>

    <div v-if="successMessage" style="color: green; margin-top: 10px;">
      {{ successMessage }}
    </div>

    <div v-if="serverError" style="color: red; margin-top: 10px;">
      {{ serverError }}
    </div>
  </form>
</template>

<script>
import axios from 'axios';

export default {
  name: 'ContactForm',
  data() {
    return {
      form: {
        name: '',
        email: '',
        message: '',
      },
      errors: {},
      successMessage: '',
      serverError: '',
      loading: false,
    };
  },
  methods: {
    async submitForm() {
      this.errors = {};
      this.successMessage = '';
      this.serverError = '';

      // Простая клиентская валидация
      if (!this.form.name) {
        this.errors.name = ['Поле Имя обязательно.'];
      }
      if (!this.form.email) {
        this.errors.email = ['Поле Email обязательно.'];
      } else if (!this.validEmail(this.form.email)) {
        this.errors.email = ['Email введён некорректно.'];
      }
      if (!this.form.message) {
        this.errors.message = ['Поле Сообщение обязательно.'];
      } else if (this.form.message.length < 5) {
        this.errors.message = ['Сообщение должно быть не менее 5 символов.'];
      }

      if (Object.keys(this.errors).length > 0) {
        return; // есть ошибки - не отправляем
      }

      this.loading = true;

      try {
        const response = await axios.post('http://127.0.0.1:8000/api/contact', this.form);
        this.successMessage = response.data.message || 'Форма успешно отправлена!';
        this.form.name = '';
        this.form.email = '';
        this.form.message = '';
      } catch (error) {
        if (error.response && error.response.status === 422) {
          // Ошибки валидации с сервера
          this.errors = error.response.data.errors || {};
        } else {
          this.serverError = 'Произошла ошибка на сервере. Попробуйте позже.';
        }
      } finally {
        this.loading = false;
      }
    },
    validEmail(email) {
      const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@(([^<>()[\]\.,;:\s@"]+\.)+[^<>()[\]\.,;:\s@"]{2,})$/i;
      return re.test(email);
    },
  },
};
</script>

<style scoped>
input, textarea {
  display: block;
  margin-bottom: 5px;
  width: 300px;
  padding: 5px;
}
button {
  padding: 5px 15px;
}
</style>
