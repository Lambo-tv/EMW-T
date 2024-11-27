<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Requests;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreWishRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<\Illuminate\Validation\Rules\Unique|string>>
     */
    public function rules(Request $request): array
    {
        $user = auth()->user();

        return [
            'movie_id' => [
                'required_if:meta,movie',
                'required_without:tv_id',
                'decimal:0',
                'min:1',
                Rule::unique('wishes')->where(fn (Builder $query) => $query->where('user_id', '=', $user->id)),
            ],
            'tv_id' => [
                'required_if:meta,tv',
                'required_without:movie_id',
                'decimal:0',
                'min:1',
                Rule::unique('wishes')->where(fn (Builder $query) => $query->where('user_id', '=', $user->id)),
            ],
            'meta' => [
                'required',
                'in:movie,tv',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'movie_id.unique' => 'You are already receiving notifications for this movie.',
            'tv_id.unique'    => 'You are already receiving notifications for this tv.',
        ];
    }
}
