<form action="/">
    <div class="container-fluid header-control">
        <div class="row align-items-end">
            <div class="col-2">
                Выберите воронку
                <select class="form-select form-select-sm">
                    <option value="" @if($pipeline == '') selected @endif>Первичные КЦ</option>
                    <option value="two" @if($pipeline == 'two') selected @endif>Продление КЦ</option>
                </select>
            </div>
            <div class="col-2">
                Выберите месяц
                <select class="form-select form-select-sm">
                    @foreach($footer['months'] as $month)
                        <option value="{{ $month->month }}" @if($footer['month'] == $month->month) selected @endif>{{ $month->monthName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-2">
                Выберите год
                <select class="form-select form-select-sm">
                    @foreach($footer['years'] as $year)
                        <option value="{{ $year->year }}" @if($footer['year'] == $year->year) selected @endif>{{ $year->year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-3">
                <button type="submit" class="btn btn-primary btn-sm">Перейти</button>
            </div>
        </div>
    </div>
</form>
