<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'table_number' => ['required', 'integer', 'min:1'],
            'order_status' => ['required', Rule::in(\App\Enum\orderStatus::values())],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_item_id' => ['required', 'integer', 'exists:menu_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
    public function messages()
    {
        return [
            'customer_id.required' => 'Customer is required.',
            'customer_id.exists' => 'Selected customer does not exist.',
            'table_number.required' => 'Table number is required.',
            'order_status.required' => 'Order status is required.',
            'order_status.in' => 'Invalid order status selected.',
            'items.required' => 'Order items are required.',
            'items.array' => 'Order items must be an array.',
            'items.min' => 'At least one order item is required.',
            'items.*.menu_item_id.required' => 'Menu item is required for each item.',
            'items.*.menu_item_id.exists' => 'Selected menu item does not exist.',
            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}
