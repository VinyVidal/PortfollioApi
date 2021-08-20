<?php
namespace App\Repositories;

use App\Models\ProjectCategory;
use App\Models\User;

class ProjectCategoryRepository {
    /**
     * @return ProjectCategory
     */
    public function byId(?int $id) {
        $category = ProjectCategory::find($id);

        return $category;
    }

    /**
     * @return ProjectCategory
     */
    public function byPosition(int $position) {
        $category = ProjectCategory::where('position', $position)
                          ->first();

        return $category;
    }

    /**
     * @return ProjectCategory[]
     */
    public function all() {
        $categories = ProjectCategory::orderBy('position')
                           ->get();

        return $categories;
    }

    /**
     * @return ProjectCategory[]
     */
    public function allByUser(User $user) {
        $categories = ProjectCategory::where('user_id', $user->id)
                           ->orderBy('position')
                           ->get();

        return $categories;
    }

    /**
     * @return ProjectCategory[]
     */
    public function allByPositionHigher(int $position, User $user, $equal = false) {
        $categories = ProjectCategory::where('user_id', $user->id)
                           ->where('position', $equal ? '>=' : '>', $position)
                           ->orderBy('position')->get();

        return $categories;
    }

    /**
     * @return ProjectCategory[]
     */
    public function allByPositionLower(int $position, User $user, $equal = false) {
        $categories = ProjectCategory::where('user_id', $user->id)
                           ->where('position', $equal ? '<=' : '<', $position)
                           ->orderBy('position')
                           ->get();

        return $categories;
    }

    /**
     * @return ProjectCategory[]
     */
    public function allByPositionBetween(int $startPosition, int $endPosition, User $user, $equal = false) {
        $categories = ProjectCategory::where('user_id', $user->id)
                           ->where('position', $equal ? '>=' : '>', $startPosition)
                           ->where('position', $equal ? '<=' : '<', $endPosition)
                           ->orderBy('position')
                           ->get();

        return $categories;
    }

    /**
     * @return int
     */
    public function maxPosition(User $user) {
        $category = ProjectCategory::where('user_id', $user->id)->orderBy('position', 'desc')->first();

        $maxPosition = 0;

        if($category) {
            $maxPosition = $category->position;
        }

        return $maxPosition;
    }
}