<?php
use App\Http\Controllers\CalendarController;


Route::get('/calendar/timeline', [CalendarController::class, 'index'])->name('calendar.timeline');
Route::get('/events', [CalendarController::class, 'getEvents'])->name('events.index');
Route::get('/events/check-availability', [CalendarController::class, 'checkAvailability'])->name('events.check-availability');
Route::post('/events', [CalendarController::class, 'store'])->name('events.store');
Route::put('/events/{id}', [CalendarController::class, 'update'])->name('events.update');
Route::post('/events/{id}/extend', [CalendarController::class, 'extend'])->name('events.extend');
Route::delete('/events/{id}', [CalendarController::class, 'destroy'])->name('events.destroy');