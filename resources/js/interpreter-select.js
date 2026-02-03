let selectAgentElement = document.querySelector('select.agent--select');
let jobType            = selectAgentElement.dataset.jobType;

let requiredInputs = jobType == 'interpreter'
    ? {
        'to_language_id'    : false,
        'skill_id'          : false,
        'interpreter_type_id' : false,
        'gender'            : false,
        'appointment_date'  : false,
        'start_time'        : false,
        'duration_hours'    : false,
        'duration_minutes'  : false,
        'security_type_id'  : false
    } : {
        'from_language_id' : false,
        'to_language_id'   : false,
        'skill_id'         : false,
        'affirmation'      : false,
        'affidavit'        : false,
        'security_type_id' : false
    };


Object.keys(requiredInputs)
    .forEach((name) => {
        let element = document.querySelector(`[name="${name}"]`);

        requiredInputs[name] = element.value !== '';

        element.addEventListener('change', (event) => {
            requiredInputs[name] = event.target.value !== '';

            handleSelect(event);

            event.target.dataset.previousValue = event.target.value;
        })
    });

/**
 * For the select element to be enabled all inputs must have a value
 *
 * @returns bool
 */
function shouldEnableSelect(){
    return Object.values(requiredInputs)
        .reduce((final, inputFilled) => {
            return final && inputFilled;
        }, true);
}

function handleSelect(event){
    let enableSelect = shouldEnableSelect();

    //we need to empty the agent select if its to be disabled or the value of a field changes
    if(!enableSelect || (event !== null && event.target.dataset.previousValue != event.target.value)){
        $(selectAgentElement).empty().trigger('change');
    }

    selectAgentElement.disabled = !enableSelect;
}

$(selectAgentElement).select2({
    width: '100%',
    ajax: {
        delay: 500,
        url: `/api/available-interpreters111/${jobType}`,
        data: (params) => {
            return Object.keys(requiredInputs)
                .reduce((query, input) => {
                    query[input] = document.querySelector(`[name="${input}"]`).value;

                    return query;
                }, params);
        },
        processResults: function (data, params) {
            params.page = params.page || 1;

            return {
                results: Object.values(data.data)
                    .map((interpreter) => {
                        return {
                            id   : interpreter.id,
                            text : interpreter.user.fullName
                        }
                    }),
                pagination: {
                    more: data.last_page != data.current_page
                }
            };
        }
    }
})

if(shouldEnableSelect()) { //on page load test if it should be open
    handleSelect(null);
}

