'''Given a target number, a non-negative integer k and an integer array A sorted in ascending order, find the k closest numbers to target in A, sorted in ascending order by the difference between the number and target. Otherwise, sorted in ascending order by number if the difference is same.

Example
Given A = [1, 2, 3], target = 2 and k = 3, return [2, 1, 3].

Given A = [1, 4, 6, 8], target = 3 and k = 3, return [4, 1, 6].

Challenge
O(logn + k) time complexity.'''

class Solution:
    """
    @param A: an integer array
    @param target: An integer
    @param k: An integer
    @return: an integer array
    """
    def kClosestNumbers(self, A, target, k):
        """
        Binary search to find the closest positions (low and high). [logN]
        Then compare abs(target-A[low]) and ans(target-A[high]), adding the closet element til results reach K-length. [K]
        """
        low, high = 0, len(A) - 1
        while low + 1 < high:
            mid = (low + high) // 2
            if A[mid] < target:
                low = mid
            else:
                high = mid
        
        result = []
        while len(result) != k:
            left_diff = abs(A[low] - target) if low >= 0 else None
            right_diff = abs(A[high] - target) if  high < len(A) else None
            
            # 这里容易出现一个bug是直接写成简写: if right_diff and left_diff
            # 而python 里 0 会被当成false，所以要显示比对
            if right_diff != None and left_diff != None:
                if right_diff < left_diff:
                    result.append(A[high])
                    high += 1
                else:
                    result.append(A[low])
                    low -= 1
            elif right_diff != None:
                result.append(A[high])
                high += 1
            elif left_diff != None:
                result.append(A[low])
                low -= 1
            else:
                break

        return result



x = Solution()
print(x.kClosestNumbers([2,4,6,8,9,10,12,13,14,15],11,5))