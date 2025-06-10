#!/bin/bash
src_encoding="cp1251"
target_encoding="UTF-8"
silent_mode=false

if [[ "$1" == "--help" ]]; then
    echo "Використання: $0 [--help | --version] | [[-s|--silent] [назва_групи] файл.csv]"
    exit 0
elif [[ "$1" == "--version" ]]; then
    echo "Версія: 1.0"
    exit 0
elif [[ "$1" == "-s" || "$1" == "--silent" ]]; then
    silent_mode=true
elif [[ "$1" == *.csv ]]; then
    input_csv="$1"
elif [[ "$2" == *.csv ]]; then
    input_csv="$2"
    chosen_group="$1"
fi

if [[ -z "$input_csv" ]]; then
    mapfile -t files_list < <(ls TimeTable_??_??_20??.csv 2>/dev/null | sort)
    if [[ ${#files_list[@]} -eq 0 ]]; then
        echo "CSV-файли не знайдені." >&2
        exit 1
    fi
    echo "Оберіть CSV-файл зі списку:"
    select f in "${files_list[@]}"; do
        if [[ -n "$f" ]]; then
            input_csv="$f"
            break
        else
            echo "Невірний вибір."
        fi
    done
fi

if [[ ! -r "$input_csv" ]]; then
    echo "Файл '$input_csv' недоступний або не існує." >&2
    exit 1
fi

utf_csv=$(mktemp)
if ! iconv -f "$src_encoding" -t "$target_encoding" "$input_csv" > "$utf_csv"; then
    echo "Помилка перекодування." >&2
    exit 1
fi

mapfile -t group_list < <(grep -o 'ПЗПІ-[0-9]\+-[0-9]\+' "$utf_csv" | sort -u)

if [[ ${#group_list[@]} -eq 0 ]]; then
    echo "Групи не знайдені у файлі." >&2
    rm -f "$utf_csv"
    exit 1
fi

selected=""
if [[ -n "$chosen_group" ]]; then
    for gr in "${group_list[@]}"; do
        if [[ "$gr" == "$chosen_group" ]]; then
            selected="$gr"
            break
        fi
    done
    if [[ -z "$selected" ]]; then
        echo "Група '$chosen_group' відсутня у файлі." >&2
        printf "Доступні групи:\n%s\n" "${group_list[@]}" >&2
        rm -f "$utf_csv"
        exit 1
    fi
else
    if [[ ${#group_list[@]} -eq 1 ]]; then
        selected="${group_list[0]}"
        echo "Автоматично вибрано: $selected"
    else
        PS3="Оберіть групу: "
        select g in "${group_list[@]}"; do
            if [[ -n "$g" ]]; then
                selected="$g"
                break
            fi
        done
    fi
fi

base_name=$(basename "$input_csv")
output_csv="Google_${base_name}"
tmp_sorted=$(mktemp)
tmp_data=$(mktemp)

sed 's/\r/\n/g' "$input_csv" | iconv -f cp1251 -t utf-8 | awk -v GR="$selected" '
BEGIN {
    FS = ","; OFS = "\t"
}
NR == 1 { next }

function date_key(dt, tm) {
    split(dt, d, ".")
    split(tm, t, ":")
    return sprintf("%04d%02d%02d%02d%02d", d[3], d[2], d[1], t[1], t[2])
}

function clean(s) {
    gsub(/^"|"$/, "", s)
    return s
}

{
    line = $0
    match(line, /"[0-3][0-9]\.[0-1][0-9]\.[0-9]{4}"/)
    if (!RSTART) next
    prefix = substr(line, 1, RSTART - 2)
    rest = substr(line, RSTART)

    n = 0; quote = 0; tmp = ""
    for (i = 1; i <= length(rest); i++) {
        c = substr(rest, i, 1)
        if (c == "\"") quote = !quote
        else if (c == "," && !quote) {
            fields[++n] = tmp
            tmp = ""
        } else {
            tmp = tmp c
        }
    }
    fields[++n] = tmp
    for (i = 1; i <= n; i++) fields[i] = clean(fields[i])
    if (n < 12) next

    match(prefix, /(ПЗПІ-[0-9]+-[0-9]+)/, m)
    grp = m[1]
    if (grp != GR) next

    subj = substr(prefix, RSTART + RLENGTH)
    gsub(/^[[:space:]-]+/, "", subj)
    gsub(/^"+|"+$/, "", subj)  
    kind = "Інше"

    if (fields[11] ~ /Лб/) kind = "Лб"
    else if (fields[11] ~ /Лк/) kind = "Лк"
    else if (fields[11] ~ /Пз/) kind = "Пз"
    else if (fields[11] ~ /Екз/i) kind = "Екз"

    key = date_key(fields[1], fields[2])
    print subj, kind, fields[1], fields[2], fields[3], fields[4], fields[11], key
}' > "$tmp_data"

sort -t $'\t' -k8,8 "$tmp_data" > "$tmp_sorted"

awk -F'\t' '
BEGIN {
    OFS = ","
    print "Subject", "Start Date", "Start Time", "End Date", "End Time", "Description"
}

function us_date(d) {
    split(d, p, ".")
    return sprintf("%02d/%02d/%04d", p[2], p[1], p[3])
}

function ampm(t) {
    split(t, h, ":")
    hour = h[1] + 0
    min = h[2]
    mer = (hour >= 12) ? "PM" : "AM"
    if (hour == 0) hour = 12
    else if (hour > 12) hour -= 12
    return sprintf("%02d:%s %s", hour, min, mer)
}

{
    id = $1 "_" $2
    dt = $3 "_" $7

    if ($2 == "Лб") {
        if (!(dt in lab_seen)) {
            counter[id]++
            lab_seen[dt] = counter[id]
        }
        idx = lab_seen[dt]
    } else {
        counter[id]++
        idx = counter[id]
    }

    title = $1 "; №" idx
    start_d = us_date($3)
    start_t = ampm($4)
    end_d = us_date($5)
    end_t = ampm($6)
    note = $7

    printf "\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n", \
        title, start_d, start_t, end_d, end_t, note
}' "$tmp_sorted" > "$output_csv"

if ! $silent_mode; then
    column -s, -t "$output_csv"
else
    echo "Готовий CSV збережено як: $output_csv"
fi

rm -f "$utf_csv" "$tmp_data" "$tmp_sorted"
