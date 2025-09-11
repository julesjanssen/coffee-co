<p>
    {{ __('Your target is to pick up :count request per year.', ['count' => $requestTarget]) }}
    {{ __('You have picked up :count requests in year :year.', ['count' => $requestCount, 'year' => $year]) }}<br/>

@if ($requestCount > $requestTarget)
    <strong>{{ __('You are above target. Try investing time in picking up more valuable requests.') }}</strong>
@elseif ($requestCount < $requestTarget)
    <strong>{{ __('You have not achieved your target. You need to pick up more requests.') }}</strong>
@else
    <strong>{{ __('You are right on target. Keep up the good work.') }}</strong>
@endif
</p>

<p>{{ __('The average client satisfaction score is :score.', ['score' => round($avgNps)]) }}</p>

@if ($profit > 0)
    <p>{{ __('The profit this year was :profit M.', ['profit' => $profit]) }}</p>
@else
    <p>{{ __('The loss this year was :loss M.', ['loss' => $profit * -1]) }}</p>
@endif
