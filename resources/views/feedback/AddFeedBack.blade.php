@extends('layouts.app')


<style>
    #signature-pad {
        border: 1px solid #000;
        width: 400px;
        height: 200px;
    }

        form {
            max-width: 600px;
            margin: auto;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input[type="radio"] {
            margin-right: 10px;
        }
        textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }
        .form-group button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #0056b3;
        }
        /* .formBorder {
            border: 1px solid;
            box-shadow: 0px 5px 11px 3px;
            padding: 10px;
        } */
    </style>

@section('content')

    <main class="section">
        <div class="section__top">
            <nav class="flex-container section__job-types">
                <div class="section__heading">FeedBack</div>
            </nav>
           
        </div>
    </main>

    <section class="content__table">
      <div class="row" >
        <div class="col-md-6" >
      @role('client')
       {{ Form::open([ 'route' => ['feedback.store' ] ,'id'=>'feedback-form','method' => 'POST' ]) }}
       @csrf
       <input type="hidden" name="job_id" value="{{ $timesheet[0]->job_id }}">
       <input type="hidden" name="client_id" value="{{ $timesheet[0]->interpreter->client->id }}">
       <input type="hidden" name="agent_id" value="{{ $timesheet[0]->agentOne->id }}">
       <div class="col-md-12 formBorder">
            <h5>Please Provide FeedBack For Agent Services</h5>

            <div class="form-group">
            <label for="appearance">Appearance:</label>
            <label><input type="radio" name="appearance" value="Excellent"  required> Excellent</label>
            <label><input type="radio" name="appearance" value="Good" > Good</label>
            <label><input type="radio" name="appearance" value="Fair" > Fair</label>
            <label><input type="radio" name="appearance" value="Poor" > Poor</label>
        </div>

        <div class="form-group">
            <label for="punctuality">Punctuality:</label>
            <label><input type="radio" name="punctuality" value="Yes"  required> Yes</label>
            <label><input type="radio" name="punctuality" value="No" > No</label>
        </div>

        <div class="form-group">
            <label for="quality">Quality of Interpreting:</label>
            <label><input type="radio" name="quality" value="Yes"  required> Yes</label>
            <label><input type="radio" name="quality" value="No" > No</label>
        </div>

        <div class="form-group">
            <label for="empathy">Empathy:</label>
            <label><input type="radio" name="empathy" value="Yes"  required> Yes</label>
            <label><input type="radio" name="empathy" value="No" > No</label>
        </div>

        <div class="form-group">
            <label for="comments">Comments:</label>
            <textarea name="comments" id="comments" placeholder="Enter your comments here..."></textarea>
        </div>

        <div class="form-group">
     
        <button type="submit">Submit Feedback</button>

           
        </div>

       </div>
                
                 
    </div>
    </div>
            
                
        {{ Form::close() }}
    </div>
    @endrole
      </div>
    </section>

       
@endsection
