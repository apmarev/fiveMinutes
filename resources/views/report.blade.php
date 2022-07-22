{{--<x-layout>--}}

{{--    <div class="selectable">--}}
{{--        <form action="/">--}}
{{--            <select name="month">--}}
{{--                @foreach($data['vars']['months'] as $el)--}}
{{--                    <option value="{{ $el->month }}" @if($data['date']['month'] == $el->month) selected @endif>{{ $el->monthName }}</option>--}}
{{--                @endforeach--}}
{{--            </select>--}}

{{--            <select name="year">--}}
{{--                @foreach($data['vars']['years'] as $el)--}}
{{--                    <option value="{{ $el->year }}" @if($data['date']['year'] == $el->year) selected @endif>{{ $el->year }}</option>--}}
{{--                @endforeach--}}
{{--            </select>--}}

{{--            <select name="pipeline">--}}
{{--                <option value="3493222" @if($data['pipeline'] == 3493222) selected @endif>Первичные КЦ</option>--}}
{{--                <option value="5084302" @if($data['pipeline'] == 5084302) selected @endif>Продление КЦ</option>--}}
{{--            </select>--}}

{{--            <button type="submit">Перейти</button>--}}
{{--        </form>--}}
{{--    </div>--}}

{{--    <div class="router">--}}
{{--        <button onclick="right()">Налево</button>--}}
{{--        <button onclick="left()">Право</button>--}}
{{--    </div>--}}

{{--    <div class="all-sum">--}}
{{--        <div class="container-fluid">--}}
{{--            <div class="row">--}}
{{--                <div class="col-3">--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-8 data green padding-left">--}}
{{--                            Общее--}}
{{--                        </div>--}}
{{--                        <div class="col-4 data green center">--}}
{{--                            Итог за месяц--}}
{{--                        </div>--}}
{{--                        @foreach($listThree as $el)--}}
{{--                            @if($data['pipeline'] == 5084302 && isset($el['visible']))--}}

{{--                            @else--}}
{{--                                <div class="col-8 data line_bottom line_right padding-left">--}}
{{--                                    {{ $el['title'] }}--}}
{{--                                </div>--}}
{{--                                <div class="col-4 data line_bottom line_right center">--}}
{{--                                    {{ number_format($data['general'][$el['key']], 2, ',', ' ') }} @if(isset($el['type'])) {{ $el['type'] }} @endif--}}
{{--                                </div>--}}
{{--                            @endif--}}
{{--                        @endforeach--}}
{{--                    </div>--}}

{{--                </div>--}}
{{--                <div class="col-9"></div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <div class="container-fluid dashboard">--}}
{{--        <div class="row">--}}
{{--            <div class="col-3 border-right">--}}

{{--                <div class="container-fluid">--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-8 data orange padding-left">--}}
{{--                            Общая выручка--}}
{{--                        </div>--}}
{{--                        <div class="col-4 data orange center">--}}
{{--                            Итог за месяц--}}
{{--                        </div>--}}
{{--                        @foreach($listOne as $el)--}}
{{--                            <div class="col-8 data line_bottom line_right padding-left indigo">--}}
{{--                                {{ $el['title'] }}--}}
{{--                            </div>--}}
{{--                            <div class="col-4 data line_bottom line_right center indigo">--}}
{{--                                {{ number_format($data['allPrice'][$el['key']], 2, ',', ' ') }} ₽--}}
{{--                            </div>--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <div class="container-fluid">--}}
{{--                    <div class="row">--}}
{{--                        @foreach($data['list'] as $el)--}}
{{--                            <div class="col-12 data green padding-left">--}}
{{--                                {{ $el['name'] }}--}}
{{--                            </div>--}}
{{--                            @foreach($listTwo as $i)--}}
{{--                                @if($data['pipeline'] == 5084302 && isset($i['visible']))--}}

{{--                                @else--}}
{{--                                    <div class="col-8 data line_bottom line_right padding-left @if(isset($i['className'])) {{$i['className']}} @endif}}">--}}
{{--                                        {{ $i['title'] }}--}}
{{--                                    </div>--}}
{{--                                    <div class="col-4 data center line_bottom @if(isset($i['className'])) {{$i['className']}} @endif">--}}
{{--                                        @if(isset($i['type']) && $i['type'] == 'input')--}}
{{--                                            <input type="number" value="{{ $el['all'][$i['key']] }}" id="{{ $el['name'] }}" onblur="setPlan(this.value, this.id, 'plan')" />--}}
{{--                                        @else--}}
{{--                                            @if(isset($el['all'][$i['key']]))--}}
{{--                                                {{ number_format($el['all'][$i['key']], 2, ',', ' ') }}  @if(isset($i['type'])) {{ $i['type'] }} @endif--}}
{{--                                            @endif--}}
{{--                                        @endif--}}
{{--                                    </div>--}}
{{--                                @endif--}}
{{--                            @endforeach--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--            </div>--}}
{{--            <div class="col-9 listing" id="listing">--}}
{{--                <div class="days">--}}
{{--                    @foreach($data['price'] as $el)--}}
{{--                        <div>--}}
{{--                            <div class="container-fluid">--}}
{{--                                <div class="row">--}}
{{--                                    <div class="col-12 data center orange line_right">--}}
{{--                                        {{ $el['title'] }}--}}
{{--                                    </div>--}}
{{--                                    <div class="col-12 data center line_bottom line_right indigo">{{ number_format($el['days']['one'], 2, ',', ' ') }} ₽</div>--}}
{{--                                    <div class="col-12 data center line_bottom line_right indigo">{{ number_format($el['days']['two'], 2, ',', ' ') }} ₽</div>--}}
{{--                                    <div class="col-12 data center line_bottom line_right indigo">{{ number_format($el['days']['three'], 2, ',', ' ') }} ₽</div>--}}
{{--                                    <div class="col-12 data center line_bottom line_right indigo">{{ number_format($el['days']['four'], 2, ',', ' ') }} ₽</div>--}}
{{--                                    <div class="col-12 data center line_bottom line_right indigo">{{ number_format($el['days']['five'], 2, ',', ' ') }} ₽</div>--}}
{{--                                    <div class="col-12 data center line_bottom line_right indigo">{{ number_format($el['days']['six'], 2, ',', ' ') }} ₽</div>--}}
{{--                                    <div class="col-12 data center line_bottom line_right indigo">{{ number_format($el['days']['seven'], 2, ',', ' ') }} ₽</div>--}}
{{--                                    <div class="col-12 data center line_bottom line_right indigo">{{ number_format($el['days']['eight'], 2, ',', ' ') }} ₽</div>--}}
{{--                                    <div class="col-12 data center line_bottom line_right indigo">{{ number_format($el['days']['nine'], 2, ',', ' ') }} ₽</div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    @endforeach--}}
{{--                </div>--}}

{{--                @foreach($data['list'] as $el)--}}
{{--                    <div class="days">--}}
{{--                        @foreach($el['days'] as $i)--}}
{{--                            <div>--}}
{{--                                <div class="col-12 data center green line_right">{{ $i['title'] }}</div>--}}

{{--                                <div class="col-12 data center line_bottom line_right @if(isset($i['week'])) blue @endif">{{ number_format($i['values']['all']['value'], 2, ',', ' ') }}</div>--}}
{{--                                <div class="col-12 data center line_bottom line_right @if(isset($i['week'])) blue @endif">{{ number_format($i['values']['monthExam']['value'], 2, ',', ' ') }} ₽</div>--}}
{{--                                <div class="col-12 data center line_bottom line_right @if(isset($i['week'])) blue @endif">{{ number_format($i['values']['monthOge']['value'], 2, ',', ' ') }} ₽</div>--}}
{{--                                <div class="col-12 data center line_bottom line_right @if(isset($i['week'])) blue @endif">{{ number_format($i['values']['monthTenClass']['value'], 2, ',', ' ') }} ₽</div>--}}
{{--                                <div class="col-12 data center line_bottom line_right @if(isset($i['week'])) blue @endif">{{ number_format($i['values']['packageExam']['value'], 2, ',', ' ') }} ₽</div>--}}
{{--                                <div class="col-12 data center line_bottom line_right @if(isset($i['week'])) blue @endif">{{ number_format($i['values']['packageOge']['value'], 2, ',', ' ') }} ₽</div>--}}
{{--                                <div class="col-12 data center line_bottom line_right @if(isset($i['week'])) blue @endif">{{ number_format($i['values']['packageTenClass']['value'], 2, ',', ' ') }} ₽</div>--}}
{{--                                <div class="col-12 data center line_bottom line_right @if(isset($i['week'])) blue @endif">{{ number_format($i['values']['countPackagesExam']['value'], 2, ',', ' ') }}</div>--}}
{{--                                <div class="col-12 data center line_bottom line_right @if(isset($i['week'])) blue @endif">{{ number_format($i['values']['countPackagesOge']['value'], 2, ',', ' ') }}</div>--}}
{{--                                <div class="col-12 data center line_bottom line_right @if(isset($i['week'])) blue @endif">{{ number_format($i['values']['countPackagesTenClass']['value'], 2, ',', ' ') }}</div>--}}

{{--                                <div class="col-12 data center line_bottom line_right blue">{{ number_format($i['values']['countPriceMonth']['value'], 2, ',', ' ') }} ₽</div>--}}
{{--                                <div class="col-12 data center line_bottom line_right blue">{{ number_format($i['values']['countPricePackage']['value'], 2, ',', ' ') }} ₽</div>--}}
{{--                                <div class="col-12 data center line_bottom line_right yellow">{{ number_format($i['values']['countMonth']['value'], 2, ',', ' ') }}</div>--}}
{{--                                <div class="col-12 data center line_bottom line_right yellow">{{ number_format($i['values']['countPackage']['value'], 2, ',', ' ') }}</div>--}}

{{--                                <div class="col-12 data center line_bottom line_right @if(isset($i['week'])) blue @endif">@if(isset($i['week'])) {{ number_format($i['values']['sumPrice']['value'], 2, ',', ' ') }} ₽ @endif</div>--}}
{{--                                <div class="col-12 data center line_bottom line_right @if(isset($i['week'])) blue @endif">@if(isset($i['week'])) {{ number_format($i['values']['countBuy']['value'], 2, ',', ' ') }} @endif</div>--}}
{{--                                <div class="col-12 data center line_bottom line_right @if(isset($i['week'])) blue @endif">@if(isset($i['week'])) {{ number_format($i['values']['averageCheck']['value'], 2, ',', ' ') }} ₽ @endif</div>--}}
{{--                                <div class="col-12 data center line_bottom line_right @if(isset($i['week'])) blue @endif">@if(isset($i['week'])) {{ number_format($i['values']['conversion']['value'], 2, ',', ' ') }} % @endif</div>--}}

{{--                                @if($data['pipeline'] != 5084302)--}}
{{--                                    <div class="col-12 data center line_bottom line_right @if(isset($i['week'])) blue @endif">--}}
{{--                                        @if(isset($i['week']))--}}
{{--                                            <input type="number" value="{{ $i['plan'] }}" id="{{ $el['name'] }}" onblur="setPlan(this.value, this.id, 'week{{ $i['weekNumber'] }}')" />--}}
{{--                                        @endif--}}
{{--                                    </div>--}}
{{--                                    <div class="col-12 data center line_bottom line_right @if(isset($i['week'])) blue @endif">--}}
{{--                                        @if(isset($i['week']))--}}
{{--                                            {{ number_format($i['values']['planPercent']['value'], 2, ',', ' ') }} %--}}
{{--                                        @endif--}}
{{--                                    </div>--}}
{{--                                    <div class="col-12 data center line_bottom line_right @if(isset($i['week'])) blue @endif">--}}
{{--                                        @if(isset($i['week']))--}}
{{--                                            {{ number_format($i['values']['planRemainder']['value'], 2, ',', ' ') }} ₽--}}
{{--                                        @endif--}}
{{--                                    </div>--}}
{{--                                @endif--}}
{{--                                <div class="col-12 data center line_bottom line_right @if(isset($i['week'])) blue @endif">--}}
{{--                                    @if(!isset($i['week']))--}}
{{--                                        <div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="{{ $el['name'] }}" onchange="setWeekend(this.id, '{{ $i['day'] }}')" @if(isset($i['values']['weekend']) && $i['values']['weekend']['value'] > 0) checked @endif /></div>--}}
{{--                                    @else--}}
{{--                                        {{ $i['values']['weekend']['value'] }}--}}
{{--                                    @endif--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                @endforeach--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <script>--}}
{{--        function setPlan(e, manager, type) {--}}
{{--            axios.post('/api/report/plan', {--}}
{{--                manager: manager,--}}
{{--                type: type,--}}
{{--                month: `{{ $data['date']['month'] }}`,--}}
{{--                year: {{ $data['date']['year'] }},--}}
{{--                value: e--}}
{{--            })--}}
{{--                .then(function (response) {--}}
{{--                    console.log(response);--}}
{{--                    reloadPage()--}}
{{--                })--}}
{{--                .catch(function (error) {--}}
{{--                    console.log(error);--}}
{{--                });--}}
{{--        }--}}

{{--        function setWeekend(manager, day) {--}}
{{--            axios.post('/api/report/weekend', {--}}
{{--                manager: manager,--}}
{{--                month: `{{ $data['date']['month'] }}`,--}}
{{--                year: {{ $data['date']['year'] }},--}}
{{--                day: day--}}
{{--            })--}}
{{--                .then(function (response) {--}}
{{--                    console.log(response);--}}
{{--                    reloadPage()--}}
{{--                })--}}
{{--                .catch(function (error) {--}}
{{--                    console.log(error);--}}
{{--                });--}}
{{--        }--}}

{{--        function reloadPage() {--}}
{{--            window.location.href = '/?pipeline={{ $data['pipeline'] }}&year={{ $data['date']['year'] }}&month={{ $data['date']['month'] }}'--}}
{{--        }--}}

{{--        function right() {--}}
{{--            const el = document.getElementById('listing')--}}
{{--            const y = el.scrollLeft--}}
{{--            console.log(y)--}}
{{--            el.scrollTo({--}}
{{--                behavior: "smooth",--}}
{{--                left: y - 200--}}
{{--            });--}}
{{--        }--}}

{{--        function left() {--}}
{{--            const el = document.getElementById('listing')--}}
{{--            const y = el.scrollLeft--}}
{{--            console.log(y)--}}
{{--            el.scrollTo({--}}
{{--                behavior: "smooth",--}}
{{--                left: y + 200--}}
{{--            });--}}
{{--        }--}}
{{--    </script>--}}

{{--</x-layout>--}}
