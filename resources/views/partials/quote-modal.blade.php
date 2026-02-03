<div class="modal fade" id="quoteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body py-4">
        <div class="mx-auto">
          <label class="mb-0">Interpreting costs:</label>
          <div class="d-flex align-items-center mt-3">
            <div class="d-flex flex-column align-self-start">
              <label class="label mb-2">Time</label>
              <div id="interpreting_time"></div>
            </div>
            <img class="mx-3 math-icon" src="/img/multiply.svg" alt="Multiply">
            <div>
              <label class="label mb-2">Cost per hour</label>
              <div id="interpreting_cost"></div>
            </div>
            <img class="ml-4 pt-3 math-icon" src="/img/equals.svg" alt="Equals">
            <div class="mt-2 pt-1 text-right form__total" id="interpreting_total"></div>
          </div>
          <label class="mt-4 mb-0">Travel time costs:</label>
          <div class="d-flex align-items-center mt-2">
            <div class="d-flex flex-column align-self-start">
              <label class="label mb-2">Time</label>
              <div id="travel_time"></div>
            </div>
            <img class="mx-3 math-icon" src="/img/multiply.svg" alt="Multiply">
            <div>
              <label class="label mb-2">Cost per hour</label>
              <div id="travel_cost"></div>
            </div>
            <img class="ml-4 pt-3 math-icon" src="/img/equals.svg" alt="Equals">
            <div class="mt-2 pt-1 text-right form__total" id="travel_total"></div>
          </div>
          <label class="mt-4 mb-0">Mileage costs:</label>
          <div class="d-flex align-items-center mt-2">
            <div class="d-flex flex-column align-self-start">
              <label class="label mb-2">Miles</label>
              <div id="mileage_miles"></div>
            </div>
            <img class="mx-3 math-icon" src="/img/multiply.svg" alt="Multiply">
            <div>
              <label class="label mb-2">Cost per mile</label>
              <div id="mileage_cost"></div>
            </div>
            <img class="ml-4 pt-3 math-icon" src="/img/equals.svg" alt="Equals">
            <div class="mt-2 pt-1 text-right form__total" id="mileage_total"></div>
          </div>
          <label class="mt-4 mb-0">Parking / other costs:</label>
          <div class="d-flex align-items-center mt-2">
            <div class="d-flex flex-column align-self-start w-50">
              <label class="label mb-2">Description</label>
              <div id="cost_description"></div>
            </div>
            <div>
              <label class="label mb-2">Cost</label>
              <div id="cost"></div>
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
              <label class="mb-0">Grand total:</label>
              <span class="ml-2" id="grand_total"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn--grey section__btn" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
  <script>
    window.addEventListener('load', function() {
      $('#quoteModal').on('show.bs.modal', function (event) {
        var quote = $(event.relatedTarget).data('quote'),
          job = quote.job || quote.matched_agent.job

        $('#interpreting_cost').text('£' + quote.interpreting_cost)
        $('#interpreting_time').text(job.formattedDuration)
        $('#interpreting_total').text('£' + (quote.interpreting_cost * job.totalHours))
        $('#travel_time').text(quote.travel_time)
        $('#travel_cost').text('£' + quote.travel_cost)
        $('#travel_total').text('£' + (quote.travel_time * quote.travel_cost))
        $('#mileage_miles').text(quote.mileage_miles)
        $('#mileage_cost').text('£' + quote.mileage_cost)
        $('#mileage_total').text('£' + (quote.mileage_miles * quote.mileage_cost))
        $('#cost_description').text(quote.cost_description)
        $('#cost').text('£' + quote.cost)
        $('#grand_total').text(quote.totalAmount)
      })
    })
  </script>
@endpush
