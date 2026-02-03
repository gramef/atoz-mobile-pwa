<template>
  <section>
    <label class="mb-0" for="interpreting_cost">Interpreting costs:</label>
    <div class="d-flex align-items-center mt-2">
      <div class="w-100 d-flex flex-column align-self-start form__input form__input--smaller">
        <label class="label mb-3" for="interpreting_cost">Time</label>
        <div>{{ formattedDuration }}</div>
      </div>
      <img class="mx-3 pt-3 math-icon" src="/img/multiply.svg" alt="Multiply">
      <div>
        <label class="label mb-2" for="interpreting_cost">Cost per hour</label>
        <div class="form__cost">
          <input class="input form__input form__input--smaller form__input--cost" type="number" id="interpreting_cost" name="interpreting_cost" min="0" step="0.01" v-model="form.interpreting_cost" required>
        </div>
      </div>
      <img class="ml-4 pt-3 math-icon" src="/img/equals.svg" alt="Equals">
      <div class="mt-2 pt-1 text-right form__total">£{{ parseFloat(interpretingTotal).toFixed(2) }}</div>
    </div>
    <label class="mb-0 mt-3" for="travel_time">Travel time costs:</label>
    <div class="d-flex align-items-center mt-2">
      <div>
        <label class="label mb-2" for="travel_time">Time</label>
        <input class="input form__input form__input--smaller" type="number" id="travel_time" name="travel_time" min="0" step="0.01" v-model="form.travel_time" required>
      </div>
      <img class="mx-3 pt-3 math-icon" src="/img/multiply.svg" alt="Multiply">
      <div>
        <label class="label mb-2" for="travel_cost">Cost per hour</label>
        <div class="form__cost">
          <input class="input form__input form__input--smaller form__input--cost" type="number" id="travel_cost" name="travel_cost" min="0" step="0.01" v-model="form.travel_cost" required>
        </div>
      </div>
      <img class="ml-4 pt-3 math-icon" src="/img/equals.svg" alt="Equals">
      <div class="mt-2 pt-1 text-right form__total">£{{ parseFloat(travelTotal).toFixed(2) }}</div>
    </div>
    <label class="mb-0 mt-3" for="mileage_miles">Mileage costs:</label>
    <div class="d-flex align-items-center mt-2">
      <div>
        <label class="label mb-2" for="mileage_miles">Miles</label>
        <input class="input form__input form__input--smaller" type="number" id="mileage_miles" name="mileage_miles" min="0" step="0.01" v-model="form.mileage_miles" required>
      </div>
      <img class="mx-3 pt-3 math-icon" src="/img/multiply.svg" alt="Multiply">
      <div>
        <label class="label mb-2" for="mileage_cost">Cost per mile</label>
        <div class="form__cost">
          <input class="input form__input form__input--smaller form__input--cost" type="number" id="mileage_cost" name="mileage_cost" min="0" step="0.01" v-model="form.mileage_cost" required>
        </div>
      </div>
      <img class="ml-4 pt-3 math-icon" src="/img/equals.svg" alt="Equals">
      <div class="mt-2 pt-1 text-right form__total">£{{ parseFloat(mileageTotal).toFixed(2) }}</div>
    </div>
    <label class="mb-0 mt-3" for="cost_description">Parking / other costs:</label>
    <div class="d-flex align-items-center mt-2 justify-content-between">
      <div class="flex-fill">
        <label class="label mb-2" for="cost_description">Description</label>
        <input class="input form__input" type="text" id="cost_description" name="cost_description" v-model="form.cost_description" required>
      </div>
      <div class="ml-5">
        <label class="label mb-2" for="cost">Cost</label>
        <div class="form__cost">
          <input class="input form__input form__input--cost form__input--smaller" type="number" min="0" step="0.01" id="cost" name="cost" v-model="form.cost" required>
        </div>
      </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-3">
      <div>
        <label class="mb-0" for="grand_total">Grand total:</label>
        <span class="ml-2">£{{ parseFloat(grandTotal).toFixed(2) }}</span>
      </div>
      <input v-if="jobCanBeQuoted" type="submit" value="Send Quote" class="btn btn--secondary btn--send-quote">
    </div>
  </section>
</template>

<script>
export default {
  props: {
    interpreterJobHours: Number,
    interpreterJobMinutes: Number,
    jobCanBeQuoted: Boolean,
  },
  computed: {
    totalHours() {
      return (this.interpreterJobMinutes / 60) + this.interpreterJobHours
    },
    interpretingTotal() {
      return this.totalHours * this.form.interpreting_cost
    },
    travelTotal() {
      return this.form.travel_time * this.form.travel_cost
    },
    mileageTotal() {
      return this.form.mileage_miles * this.form.mileage_cost
    },
    grandTotal() {
      return this.interpretingTotal + this.travelTotal + this.mileageTotal + (this.form.cost / 1)
    }
  },
  data() {
     return {
       formattedDuration: `${this.interpreterJobHours} hours ${this.interpreterJobMinutes} minutes`,
       form: {
         interpreting_cost: 0,
         travel_time: 0,
         travel_cost: 0,
         mileage_miles: 0,
         mileage_cost: 0,
         cost_description: '',
         cost: 0
       },
     }
  }
}
</script>