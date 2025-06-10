#!/bin/bash

# Function to print a line with padding and a specific character
print_line() {
    local padding=$1
    local count=$2
    local char=$3

    local i=0
    while [ $i -lt $padding ]; do
        printf " "
        i=$((i+1))
    done

    local j=0
    until [ $j -ge $count ]; do
        printf "%s" "$char"
        j=$((j+1))
    done

    printf "\n"
}

# Check for the correct number of arguments
if [ "$#" -ne 2 ]; then
    echo "Error: Two arguments required." >&2
    exit 1
fi

# Validate that arguments are positive integers
for arg in "$@"; do
    if ! [[ $arg =~ ^[1-9][0-9]*$ ]]; then
        echo "Error: Arguments must be positive integers." >&2
        exit 1
    fi
done

height=$1
snow_width=$2

# Ensure snow_width is an odd number
if [ $((snow_width % 2)) -eq 0 ]; then
    snow_width=$((snow_width - 1))
fi

# Validate the height based on the width
required_height=$((snow_width + 1))
if [ $height -gt $required_height ]; then
    height=$required_height
elif [ $height -lt $required_height ]; then
    echo "Error: Cannot draw tree with given parameters." >&2
    exit 1
fi

# Calculate tree component heights
branch_height=$((height - 3))
tier1_rows=$(((snow_width - 1) / 2))
tier2_rows=$(((snow_width - 3) / 2))

# Check for parameter consistency
if [ $branch_height -ne $((tier1_rows + tier2_rows)) ]; then
    echo "Error: Inconsistent parameters." >&2
    exit 1
fi

# Draw the first tier of branches
for ((i=1; i<=tier1_rows; i++)); do
    if [ $((i % 2)) -eq 1 ]; then
        symbol="*"
    else
        symbol="#"
    fi
    num_symbols=$((1 + 2*(i-1)))
    padding=$(((snow_width - num_symbols) / 2))
    print_line "$padding" "$num_symbols" "$symbol"
done

# Draw the second tier of branches
for ((j=1; j<=tier2_rows; j++)); do
    if [ $((j % 2)) -eq 1 ]; then
        symbol="#"
    else
        symbol="*"
    fi
    num_symbols=$((3 + 2*(j-1)))
    padding=$(((snow_width - num_symbols) / 2))
    print_line "$padding" "$num_symbols" "$symbol"
done

# Draw the trunk
trunk_width=3
trunk_padding=$(((snow_width - trunk_width) / 2))

for line in 1 2; do
    print_line "$trunk_padding" "$trunk_width" "#"
done

# Draw the snow layer at the bottom
for ((k=0; k<1; k++)); do
    print_line 0 "$snow_width" "*"
done