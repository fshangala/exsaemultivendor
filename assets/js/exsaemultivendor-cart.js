const Cart = {
  items:[],
  total:()=>{
    var totalPrice = 0;
    Cart.items.forEach(item => {
      totalPrice += item.price * item.quantity;
    });
    return totalPrice;
  },
  add: (listing_id, quantity, price) => {
    const item = Cart.items.find(item => item.listing_id === listing_id);
    if (item) {
      item.quantity += quantity;
    } else {
      Cart.items.push({ listing_id, quantity, price });
    }
    Cart.save();
  },
  remove: (listing_id) => {
    const index = Cart.items.findIndex(item => item.listing_id === listing_id);
    if (index !== -1) {
      Cart.items.splice(index, 1);
    }
    Cart.save();
  },
  update: (listing_id, quantity) => {
    const item = Cart.items.find(item => item.listing_id === listing_id);
    if (item) {
      item.quantity = quantity;
    }
    Cart.save();
  },
  save: () => {
    localStorage.setItem('cart', JSON.stringify(Cart.items));
    Cart.load(); // Load cart after saving to ensure UI is updated
  },
  clear: () => {
    Cart.items = [];
    localStorage.removeItem('cart');
  },
  load: () => {
    const savedCart = localStorage.getItem('cart');
    if (savedCart) {
      Cart.items = JSON.parse(savedCart);
    }
  },
  render: () => {
    Cart.load();
    const cartContainer = document.querySelector('.cart-container');
    if(cartContainer === null) return; // Ensure cartContainer exists

    cartContainer.innerHTML = ''; // Clear previous content

    const table = document.createElement('table');
    cartContainer.appendChild(table);
    table.classList.add('w-100');

    const tableHead = document.createElement('thead');
    table.appendChild(tableHead);

    const headRow = document.createElement('tr');
    tableHead.appendChild(headRow);

    const headers = ['Listing ID', 'Quantity', 'Price', 'Actions'];
    headers.forEach(header => {
      const th = document.createElement('th');
      th.innerText = header;
      headRow.appendChild(th);
    });

    const tableBody = document.createElement('tbody');
    table.appendChild(tableBody);

    Cart.items.forEach(item => {
      const row = document.createElement('tr');
      tableBody.appendChild(row);

      const listingIdCell = document.createElement('td');
      row.appendChild(listingIdCell);
      listingIdCell.innerText = item.listing_id;

      const quantityCell = document.createElement('td');
      row.appendChild(quantityCell);
      quantityCell.innerText = item.quantity;

      const priceCell = document.createElement('td');
      row.appendChild(priceCell);
      priceCell.innerText = `$${item.price}`;

      const actionsCell = document.createElement('td');
      row.appendChild(actionsCell);

      const removeButton = document.createElement('button');
      actionsCell.appendChild(removeButton);
      removeButton.innerText = 'Remove';
      removeButton.classList.add('btn','btn-danger');
      removeButton.onclick = () => {
        Cart.remove(item.listing_id);
        Cart.render(); // Re-render the cart after removing an item
      };
    });

    const totalRow = document.createElement('tr');
    tableBody.appendChild(totalRow);

    const totalCell = document.createElement('td');
    totalRow.appendChild(totalCell);
    totalCell.colSpan = 2;
    totalCell.innerText = 'Total:';

    const totalValueCell = document.createElement('td');
    totalRow.appendChild(totalValueCell);
    totalValueCell.innerText = `${Cart.total()}`;

    const cartActions = document.createElement('div');
    cartContainer.appendChild(cartActions);
    cartActions.classList.add('flex','flex-row','flex-wrap','justify-content-center','gap-2');

    const clearButton = document.createElement('button');
    cartActions.appendChild(clearButton);
    clearButton.innerText = 'Clear Cart';
    clearButton.classList.add('btn','btn-danger');
    clearButton.onclick = () => {
      Cart.clear();
      Cart.render(); // Re-render the cart after clearing
    };

    const checkoutForm = document.createElement("form");
    cartActions.appendChild(checkoutForm);
    checkoutForm.setAttribute("method","POST");

    const itemsInput = document.createElement("input");
    checkoutForm.appendChild(itemsInput);
    itemsInput.setAttribute("type","hidden");
    itemsInput.setAttribute("name","items");
    itemsInput.setAttribute("value",JSON.stringify(Cart.items));

    const submitButton = document.createElement("input");
    checkoutForm.appendChild(submitButton);
    submitButton.setAttribute("type","submit");
    submitButton.setAttribute("name","cart");
    submitButton.setAttribute("value","checkout");
    submitButton.classList.add('btn','btn-primary');
  },
}

document.addEventListener('DOMContentLoaded', () => {
  Cart.render();
});