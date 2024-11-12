<table style="
    width: 100%;
    border-collapse: collapse;
    font-family: Arial, sans-serif;
    margin: 20px 0;
    background-color: #ffffff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
">
    <thead style="background-color: #f4f6f8; text-align: left;">
    <tr>
        <th style="
                padding: 12px 16px;
                font-weight: bold;
                color: #333;
                border-bottom: 1px solid #e0e0e0;
            ">Product Title</th>
        <th style="
                padding: 12px 16px;
                font-weight: bold;
                color: #333;
                border-bottom: 1px solid #e0e0e0;
            ">Handle</th>
        <th style="
                padding: 12px 16px;
                font-weight: bold;
                color: #333;
                border-bottom: 1px solid #e0e0e0;
            ">Price</th>
        <th style="
                padding: 12px 16px;
                font-weight: bold;
                color: #333;
                border-bottom: 1px solid #e0e0e0;
            ">Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($products as $product)
        <tr style="transition: background-color 0.2s;">
            <td style="
                    padding: 12px 16px;
                    border-bottom: 1px solid #f0f0f0;
                    color: #555;
                ">{{ $product->title }}</td>
            <td style="
                    padding: 12px 16px;
                    border-bottom: 1px solid #f0f0f0;
                    color: #555;
                ">{{ $product->handle }}</td>
            <td style="
                    padding: 12px 16px;
                    border-bottom: 1px solid #f0f0f0;
                    color: #555;
                ">{{ $product->variants[0]->price }}</td>


            <td style="
                    padding: 12px 16px;
                    border-bottom: 1px solid #f0f0f0;
                    color: #555;
                ">
                    <span style="
                        display: inline-block;
                        padding: 6px 12px;
                        font-size: 14px;
                        font-weight: bold;
                        border-radius: 20px;
                        text-transform: capitalize;
                        background-color: {{ $product->status == 'active' ? '#4CAF50' : ($product->status == 'draft' ? '#FFC107' : '#F44336') }};
                        color: white;
                        transition: background-color 0.3s ease-in-out;
                    ">
                        {{ $product->status }}
                    </span>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
