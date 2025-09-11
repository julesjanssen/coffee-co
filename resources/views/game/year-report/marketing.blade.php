<p>
    {{ __('Your target is to do :count campaigns per year.', ['count' => $campaignTarget]) }}
    {{ __('You have done :count campaigns in year :year.', ['count' => $campaignCount, 'year' => $year]) }}<br/>

@if ($campaignCount > $campaignTarget)
    <strong>{{ __('You are above target. Mind your investment costs.') }}</strong>
@elseif ($campaignCount < $campaignTarget)
    <strong>{{ __('You have not achieved your target. You need to do more campaigns.') }}</strong>
@else
    <strong>{{ __('You are right on target. Keep up the good work.') }}</strong>
@endif
</p>

@if ($profit > 0)
    <p>{{ __('The profit this year was :profit M.', ['profit' => $profit]) }}</p>
@else
    <p>{{ __('The loss this year was :loss M.', ['loss' => $profit * -1]) }}</p>
@endif
