# Cache mess

This is a simple benchmark of PSR-16 and PSR-6 implementations.

## Running & cleaning up

Make sure you have Docker and `docker-compose` installed, then run:

```bash
$ docker-compose build \
    && docker-compose run --rm runner \
    && docker-compose down
```

The output will be something similar to (which was the result of my latest execution):

```
PhpBench @git_tag@. Running benchmarks.
Using configuration file: /app/phpbench.json

................................. 

33 subjects, 165 iterations, 3,300 revs, 0 rejects, 0 failures, 0 warnings
(best [mean mode] worst) = 27.130 [939.401 935.412] 32.460 (μs)
⅀T: 155,001.170μs μSD/r 15.040μs μRSD/r: 3.864%
suite: 13417e60c994960adb08c008f9b539ed5f321207, date: 2019-12-06, stime: 00:52:55
+----------------------+------------------+-----+------+-----+------------+-------------+-------------+-------------+-------------+-----------+--------+---------+
| benchmark            | subject          | set | revs | its | mem_peak   | best        | mean        | mode        | worst       | stdev     | rstdev | diff    |
+----------------------+------------------+-----+------+-----+------------+-------------+-------------+-------------+-------------+-----------+--------+---------+
| MultiSaveWithoutTTL  | benchPsr16Roave  | 0   | 100  | 5   | 2,045,768b | 1,536.100μs | 1,553.350μs | 1,553.184μs | 1,571.020μs | 11.422μs  | 0.74%  | 51.87x  |
| MultiSaveWithoutTTL  | benchPsr16Naive  | 0   | 100  | 5   | 2,008,648b | 1,318.270μs | 1,342.242μs | 1,329.105μs | 1,395.170μs | 28.122μs  | 2.10%  | 44.82x  |
| MultiSaveWithoutTTL  | benchPsr6Symfony | 0   | 100  | 5   | 2,184,736b | 2,614.600μs | 2,655.400μs | 2,639.566μs | 2,726.510μs | 37.701μs  | 1.42%  | 88.67x  |
| SingleCacheHit       | benchPsr16Roave  | 0   | 100  | 5   | 1,494,488b | 30.170μs    | 34.314μs    | 33.251μs    | 39.830μs    | 3.513μs   | 10.24% | 1.15x   |
| SingleCacheHit       | benchPsr16Naive  | 0   | 100  | 5   | 1,494,488b | 28.860μs    | 31.424μs    | 31.877μs    | 34.700μs    | 2.214μs   | 7.05%  | 1.05x   |
| SingleCacheHit       | benchPsr6Symfony | 0   | 100  | 5   | 1,494,496b | 32.840μs    | 35.916μs    | 34.074μs    | 39.480μs    | 2.674μs   | 7.44%  | 1.20x   |
| MultiCacheMiss       | benchPsr16Roave  | 0   | 100  | 5   | 2,144,432b | 2,194.220μs | 2,222.524μs | 2,215.042μs | 2,261.990μs | 22.593μs  | 1.02%  | 74.21x  |
| MultiCacheMiss       | benchPsr16Naive  | 0   | 100  | 5   | 1,647,408b | 774.780μs   | 788.084μs   | 794.631μs   | 802.420μs   | 11.402μs  | 1.45%  | 26.32x  |
| MultiCacheMiss       | benchPsr6Symfony | 0   | 100  | 5   | 1,800,360b | 1,154.010μs | 1,164.676μs | 1,162.677μs | 1,178.210μs | 8.203μs   | 0.70%  | 38.89x  |
| SingleSaveWithoutTTL | benchPsr16Roave  | 0   | 100  | 5   | 1,492,192b | 31.960μs    | 35.088μs    | 35.994μs    | 36.490μs    | 1.705μs   | 4.86%  | 1.17x   |
| SingleSaveWithoutTTL | benchPsr16Naive  | 0   | 100  | 5   | 1,492,192b | 33.870μs    | 34.592μs    | 34.977μs    | 35.240μs    | 0.575μs   | 1.66%  | 1.16x   |
| SingleSaveWithoutTTL | benchPsr6Symfony | 0   | 100  | 5   | 1,492,288b | 40.580μs    | 42.234μs    | 41.504μs    | 45.300μs    | 1.667μs   | 3.95%  | 1.41x   |
| SingleCacheMiss      | benchPsr16Roave  | 0   | 100  | 5   | 1,458,232b | 55.170μs    | 58.250μs    | 58.489μs    | 60.900μs    | 1.928μs   | 3.31%  | 1.95x   |
| SingleCacheMiss      | benchPsr16Naive  | 0   | 100  | 5   | 1,458,232b | 30.620μs    | 32.054μs    | 31.318μs    | 34.920μs    | 1.530μs   | 4.77%  | 1.07x   |
| SingleCacheMiss      | benchPsr6Symfony | 0   | 100  | 5   | 1,490,936b | 32.420μs    | 35.062μs    | 34.070μs    | 37.350μs    | 1.971μs   | 5.62%  | 1.17x   |
| SingleRemove         | benchPsr16Roave  | 0   | 100  | 5   | 1,493,136b | 28.480μs    | 30.720μs    | 29.526μs    | 35.330μs    | 2.489μs   | 8.10%  | 1.03x   |
| SingleRemove         | benchPsr16Naive  | 0   | 100  | 5   | 1,493,136b | 27.130μs    | 29.948μs    | 29.112μs    | 32.460μs    | 1.994μs   | 6.66%  | 1.00x   |
| SingleRemove         | benchPsr6Symfony | 0   | 100  | 5   | 1,493,144b | 27.550μs    | 31.894μs    | 29.746μs    | 40.910μs    | 4.659μs   | 14.61% | 1.06x   |
| MultiRemove          | benchPsr16Roave  | 0   | 100  | 5   | 2,008,000b | 958.510μs   | 983.914μs   | 972.116μs   | 1,033.270μs | 25.807μs  | 2.62%  | 32.85x  |
| MultiRemove          | benchPsr16Naive  | 0   | 100  | 5   | 1,752,000b | 544.770μs   | 560.210μs   | 549.931μs   | 602.100μs   | 21.221μs  | 3.79%  | 18.71x  |
| MultiRemove          | benchPsr6Symfony | 0   | 100  | 5   | 1,774,656b | 601.380μs   | 616.242μs   | 607.786μs   | 650.980μs   | 17.889μs  | 2.90%  | 20.58x  |
| MultiSaveWithTTL     | benchPsr16Roave  | 0   | 100  | 5   | 2,102,864b | 2,665.150μs | 2,679.100μs | 2,685.460μs | 2,688.630μs | 9.344μs   | 0.35%  | 89.46x  |
| MultiSaveWithTTL     | benchPsr16Naive  | 0   | 100  | 5   | 2,004,584b | 2,445.790μs | 2,508.956μs | 2,456.522μs | 2,719.980μs | 106.004μs | 4.23%  | 83.78x  |
| MultiSaveWithTTL     | benchPsr6Symfony | 0   | 100  | 5   | 2,197,056b | 3,168.970μs | 3,214.822μs | 3,194.446μs | 3,274.810μs | 37.919μs  | 1.18%  | 107.35x |
| SingleSaveWithTTL    | benchPsr16Roave  | 0   | 100  | 5   | 1,492,424b | 34.870μs    | 38.848μs    | 36.564μs    | 48.190μs    | 4.761μs   | 12.26% | 1.30x   |
| SingleSaveWithTTL    | benchPsr16Naive  | 0   | 100  | 5   | 1,492,424b | 31.400μs    | 33.804μs    | 34.699μs    | 35.690μs    | 1.714μs   | 5.07%  | 1.13x   |
| SingleSaveWithTTL    | benchPsr6Symfony | 0   | 100  | 5   | 1,492,520b | 39.580μs    | 41.398μs    | 41.940μs    | 42.830μs    | 1.146μs   | 2.77%  | 1.38x   |
| MultiCacheHit        | benchPsr16Roave  | 0   | 100  | 5   | 2,152,424b | 1,572.610μs | 1,594.104μs | 1,595.512μs | 1,613.530μs | 13.501μs  | 0.85%  | 53.23x  |
| MultiCacheHit        | benchPsr16Naive  | 0   | 100  | 5   | 2,009,856b | 1,093.770μs | 1,102.302μs | 1,103.666μs | 1,108.820μs | 4.926μs   | 0.45%  | 36.81x  |
| MultiCacheHit        | benchPsr6Symfony | 0   | 100  | 5   | 2,009,864b | 1,777.740μs | 1,809.102μs | 1,792.159μs | 1,877.310μs | 35.552μs  | 1.97%  | 60.41x  |
| MultiCacheHitAndMiss | benchPsr16Roave  | 0   | 100  | 5   | 2,188,760b | 2,063.210μs | 2,080.002μs | 2,073.131μs | 2,110.150μs | 15.967μs  | 0.77%  | 69.45x  |
| MultiCacheHitAndMiss | benchPsr16Naive  | 0   | 100  | 5   | 2,014,424b | 1,331.830μs | 1,339.262μs | 1,334.577μs | 1,349.160μs | 6.890μs   | 0.51%  | 44.72x  |
| MultiCacheHitAndMiss | benchPsr6Symfony | 0   | 100  | 5   | 2,014,432b | 2,178.780μs | 2,240.396μs | 2,271.934μs | 2,293.760μs | 47.306μs  | 2.11%  | 74.81x  |
+----------------------+------------------+-----+------+-----+------------+-------------+-------------+-------------+-------------+-----------+--------+---------+
```

