<?php

namespace App\Services;

use App\Models\AssignedDomework;
use App\Models\User;
use Carbon\Carbon;

class DomeworkReportService
{
    public function getDomeworkStats($startDate, $endDate)
    {
        // Get all SE users
        $seUsers = User::where('role', 'se')->get();

        $above_75 = [];
        $above_50 = [];
        $above_25 = [];
        $below_25 = [];

        foreach ($seUsers as $user) {
            $completionRate = $this->calculateUserDomeworkCompletionRate($user->id, $startDate, $endDate);

            if ($completionRate === null) {
                // User has no domework assignments in this period - skip or handle as needed
                continue;
            }

            if ($completionRate >= 75) {
                $above_75[] = $user;
            } elseif ($completionRate >= 50) {
                $above_50[] = $user;
            } elseif ($completionRate >= 25) {
                $above_25[] = $user;
            } else {
                $below_25[] = $user;
            }
        }

        return [
            'above_75' => $above_75,
            'above_50' => $above_50,
            'above_25' => $above_25,
            'below_25' => $below_25,
        ];
    }

    public function calculateUserDomeworkCompletionRate($userId, $startDate, $endDate)
    {
        // Get all assigned domeworks for this user in the specified week
        $assignedDomeworks = AssignedDomework::where('user_id', $userId)
            ->whereHas('session', function ($query) use ($startDate, $endDate) {
                $query->whereHas('schedules', function ($subQuery) use ($startDate, $endDate) {
                    $subQuery->whereBetween('schedule_date', [
                        Carbon::parse($startDate)->startOfDay(),
                        Carbon::parse($endDate)->endOfDay()
                    ]);
                });
            })
            ->get();

        if ($assignedDomeworks->isEmpty()) {
            return null; // No domework assignments in this period
        }

        $totalAssigned = $assignedDomeworks->count();
        $completed = $assignedDomeworks->where('status', '1')->count(); // Assuming 1 = completed

        if ($totalAssigned == 0) {
            return null;
        }

        return round(($completed / $totalAssigned) * 100);
    }

    public function getUserDomeworkDetails($userId, $startDate, $endDate)
    {
        // Get all assigned domeworks for this user in the specified period
        $assignedDomeworks = AssignedDomework::where('user_id', $userId)
            ->whereHas('session', function ($query) use ($startDate, $endDate) {
                $query->whereHas('schedules', function ($subQuery) use ($startDate, $endDate) {
                    $subQuery->whereBetween('schedule_date', [
                        Carbon::parse($startDate)->startOfDay(),
                        Carbon::parse($endDate)->endOfDay()
                    ]);
                });
            })
            ->with(['domework', 'session'])
            ->get();

        $totalAssigned = $assignedDomeworks->count();
        $completed = $assignedDomeworks->where('status', '1')->count();
        $pending = $assignedDomeworks->where('status', '0')->count();

        $completionRate = $totalAssigned > 0 ? round(($completed / $totalAssigned) * 100) : 0;

        return [
            'total_assigned' => $totalAssigned,
            'completed' => $completed,
            'pending' => $pending,
            'completion_rate' => $completionRate,
            'domeworks' => $assignedDomeworks->map(function ($assignment) {
                return [
                    'id' => $assignment->id,
                    'title' => $assignment->domework->title ?? 'N/A',
                    'session_id' => $assignment->session->id,
                    'session_name' => $assignment->session->session_name ?? 'N/A',
                    'status' => $assignment->status,
                    'status_label' => $assignment->status == '1' ? 'Completed' : 'Pending',
                    'assigned_at' => $assignment->created_at->format('Y-m-d H:i:s'),
                    'answer' => $assignment->domework_answer,
                ];
            }),
        ];
    }

    /**
     * Get summary statistics for a group of users
     */
    public function getGroupDomeworkSummary($userIds, $startDate, $endDate)
    {
        $summary = [];

        foreach ($userIds as $userId) {
            $details = $this->getUserDomeworkDetails($userId, $startDate, $endDate);
            $user = User::find($userId);

            if ($user && $details['total_assigned'] > 0) {
                $summary[] = [
                    'user_id' => $userId,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'user_phone' => $user->phone,
                    'user_city' => $user->city,
                    'total_assigned' => $details['total_assigned'],
                    'completed' => $details['completed'],
                    'pending' => $details['pending'],
                    'completion_rate' => $details['completion_rate'],
                    'domeworks' => $details['domeworks'],
                ];
            }
        }

        // Sort by completion rate descending
        usort($summary, function ($a, $b) {
            return $b['completion_rate'] <=> $a['completion_rate'];
        });

        return $summary;
    }

    public function getDateRange($period, $startDate = null, $endDate = null)
    {
        $now = Carbon::now();

        switch ($period) {
            case 'this_week':
                $start = $now->copy()->startOfWeek(Carbon::MONDAY);
                $end = $now->copy()->endOfWeek(Carbon::SUNDAY);
                break;
            case 'last_week':
                $start = $now->copy()->subWeek()->startOfWeek(Carbon::MONDAY);
                $end = $now->copy()->subWeek()->endOfWeek(Carbon::SUNDAY);
                break;
            case 'two_weeks':
                $start = $now->copy()->subWeeks(2)->startOfWeek(Carbon::MONDAY);
                $end = $now->copy()->endOfWeek(Carbon::SUNDAY);
                break;
            case 'six_weeks':
                $start = $now->copy()->subWeeks(6)->startOfWeek(Carbon::MONDAY);
                $end = $now->copy()->endOfWeek(Carbon::SUNDAY);
                break;
            case 'this_month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'last_month':
                $start = $now->copy()->subMonth()->startOfMonth();
                $end = $now->copy()->subMonth()->endOfMonth();
                break;
            case 'custom':
                $start = Carbon::parse($startDate);
                $end = Carbon::parse($endDate);
                break;
            default:
                $start = $now->copy()->startOfWeek(Carbon::MONDAY);
                $end = $now->copy()->endOfWeek(Carbon::SUNDAY);
        }

        return [
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
            'start_label' => $start->format('M d, Y'),
            'end_label' => $end->format('M d, Y'),
        ];
    }
}
